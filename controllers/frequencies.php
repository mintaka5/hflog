<?php
require_once 'globals.php';

switch(\Ode\Manager::getInstance()->getMode()) {
	default:
		/**
		 * unset session variable holding county data
		 *
		 */
		unset($_SESSION['_county']);
		
		$states = \Ode\DBO::getInstance()->query("
			SELECT state.*
			FROM states AS state
			WHERE state.is_active = 1
			ORDER BY state.name
			ASC
		")->fetchAll(PDO::FETCH_OBJ);
		
		$sql = "SELECT a.*
				FROM frequencies AS a
				WHERE a.is_active = 1
				AND (a.modified BETWEEN " .  \Ode\DBO::getInstance()->quote(date("Y-m-d H:i:s", strtotime("-1 week")), PDO::PARAM_STR) . "
				AND " . \Ode\DBO::getInstance()->quote(date("Y-m-d H:i:s"), PDO::PARAM_STR) . ")
				ORDER BY a.created
				DESC";
		$recents = \Ode\DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, "Ode_DBO_Frequency_Model");
		
		$counties = array();
		foreach($recents as $num => $recent) {
			$counties[$recent->county()->cid]['county'] = $recent->county();
			$counties[$recent->county()->cid]['groups'][$recent->group()->id]['group'] = $recent->group();
			$counties[$recent->county()->cid]['groups'][$recent->group()->id]['freqs'][] = $recent;
		}
		$newFreqs = new ArrayObject($counties);
		
		\Ode\View::getInstance()->assign("recents", $newFreqs);
		\Ode\View::getInstance()->assign("states", $states);
		break;
	case 'search':
		$freqs = false;
		if(isset($_POST['freq'])) {
			$sql = "SELECT a.*
					FROM frequencies AS a
					WHERE a.is_active = 1
					AND a.frequency = " . trim($_POST['freq']) . "
					ORDER BY a.frequency
					ASC";
			//echo $sql;
			$freqs = \Ode\DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, "Ode_DBO_Frequency_Model");
		}
		
		\Ode\View::getInstance()->assign("freqs", $freqs);
		break;
	case 'add':
		Ode_Controller::getInstance()->requireAuth(true);
		
		switch(\Ode\Manager::getInstance()->getTask()) {
			default:
				if(Ode_Controller::getInstance()->getAuth()->isAuth()) {
					/**
					 * States for drop down menu
					 * @var stdClass
					 */
					$states = \Ode\DBO::getInstance()->query("
						SELECT 
							state.name,
							state.abbrev
						FROM states AS state
						WHERE state.is_active = 1
						ORDER BY state.name
						ASC
					")->fetchAll(PDO::FETCH_OBJ);

					\Ode\View::getInstance()->assign("states", $states);
				}
				break;
			case 'multi':
				/**
				* make sure county data is available
				* or else send back to beginning
				*/
				if(!isset($_SESSION['_county']) || empty($_SESSION['_county'])) {
					header("Location: " . \Ode\Manager::getInstance()->friendlyAction("frequencies", "add"));
					exit();
				}
				
				/**
				* CTCSS tone frequencies for drop down menu
				* @var stdClass
				*/
				$ctcssTones = \Ode\DBO::getInstance()->query("
									SELECT ctcss.id, ctcss.hertz
									FROM ctcss_tones AS ctcss
									ORDER BY ctcss.hertz
									ASC
								")->fetchAll(PDO::FETCH_OBJ);
				
				/**
				 * DCS codes for drop down menu
				 * @var unknown_type
				 */
				$dcsTones = \Ode\DBO::getInstance()->query("
									SELECT dcs.id, dcs.dcs
									FROM dcs_tones AS dcs
									ORDER BY dcs.dcs
									ASC
								")->fetchAll(PDO::FETCH_OBJ);
				
				$modes = \Ode\DBO::getInstance()->query("
									SELECT mode.*
									FROM frequency_modes AS mode
									ORDER BY mode.title
									ASC
								")->fetchAll(PDO::FETCH_CLASS, "Ode_DBO_Frequency_Mode_Model");
				
				\Ode\View::getInstance()->assign("ctcss", $ctcssTones);
				\Ode\View::getInstance()->assign("dcs", $dcsTones);
				\Ode\View::getInstance()->assign("modes", $modes);
				break;
			case 'info':
				/**
				 * make sure county data is available
				 * or else send back to beginning
				 */
				if(!isset($_SESSION['_county']) || empty($_SESSION['_county'])) {
					header("Location: " . \Ode\Manager::getInstance()->friendlyAction("frequencies", "add"));
					exit();
				}
				
				/**
				 * CTCSS tone frequencies for drop down menu
				 * @var stdClass
				 */
				$ctcssTones = \Ode\DBO::getInstance()->query("
					SELECT ctcss.id, ctcss.hertz
					FROM ctcss_tones AS ctcss
					ORDER BY ctcss.hertz
					ASC
				")->fetchAll(PDO::FETCH_OBJ);
				
				/**
				 * DCS codes for drop down menu
				 * @var unknown_type
				 */
				$dcsTones = \Ode\DBO::getInstance()->query("
					SELECT dcs.id, dcs.dcs
					FROM dcs_tones AS dcs
					ORDER BY dcs.dcs
					ASC
				")->fetchAll(PDO::FETCH_OBJ);
				
				$modes = \Ode\DBO::getInstance()->query("
					SELECT mode.*
					FROM frequency_modes AS mode
					ORDER BY mode.title
					ASC
				")->fetchAll(PDO::FETCH_CLASS, "Ode_DBO_Frequency_Mode_Model");
				
				$form = new HTML_QuickForm2("addNewFrequency");
				$form->setAttribute("action", \Ode\Manager::getInstance()->friendlyAction("frequencies", "add", "info"));
				
				$freqTxt = $form->addText("freq")->setLabel("Frequency");
				$freqTxt->addRule("required", "Required");
				$freqTxt->addRule("regex", "Not a valid format", "/\d{1,4}\.\d{3,4}/");
				
				$tagTxt = $form->addText("tag")->setLabel("Alpha Tag");
				
				$ctcssSel = $form->addSelect("ctcss")->setLabel("CTCSS tone");
				$ctcssSel->addOption("n/a", "");
				foreach($ctcssTones as $ctcss) {
					$ctcssSel->addOption($ctcss->hertz, $ctcss->id);
				}
				
				$dcsSel = $form->addSelect("dcs")->setLabel("DCS code");
				$dcsSel->addOption("n/a", "");
				foreach($dcsTones as $dcs) {
					$dcsSel->addOption($dcs->dcs, $dcs->id);
				}
				
				$nacTxt = $form->addText("nac")->setLabel("NAC Tone (P25 mode)");
				$nacTxt->setAttribute("maxlength", 3);
				$nacTxt->setAttribute("size", 4);
				$nacTxt->addRule("regex", "Letters and numbers only.", "/[A-z0-9]{3}/");
				
				$encChk = $form->addCheckbox("isencrypted")->setContent("Is this frequency encrypted?");
				
				$modeSel = $form->addSelect("mode")->setLabel("Mode");
				$modeSel->addOption("- select -", "");
				$modeSel->addRule("required", "Required");
				foreach($modes as $mode) {
					$modeSel->addOption($mode->title, $mode->id);
				}
				
				$descTxt = $form->addTextarea("description")->setLabel("Description");
				$descTxt->setAttribute("rows", 5);
				$descTxt->setAttribute("cols", 45);
				$descTxt->addRule("required", "Required");
				
				$submitBtn = $form->addSubmit()->setValue("Submit");
				
				if($form->validate()) {
					//Util::debug($_POST);
					
					Ode_DBO_Frequency::addFrequency(
						$_SESSION['_county'], $_POST[$freqTxt->getName()], $_POST[$tagTxt->getName()], 
						$_POST[$ctcssSel->getName()], $_POST[$dcsSel->getName()], $_POST[$nacTxt->getName()],
						(isset($_POST[$encChk->getName()])) ? 1 : 0,
						$_POST[$modeSel->getName()], $_POST[$descTxt->getName()], \Ode\Auth::getInstance()->getSession()->id
					);
					
					// remove session variable for county
					unset($_SESSION['_county']);
					
					header("Location: " . \Ode\Manager::getInstance()->friendlyAction("user", "frequencies"));
					exit();
				}
				
				\Ode\View::getInstance()->assign("form", $form->render(\Ode\View::getInstance()->getFormRenderer()));
				break;
		}
		break;
	case 'admin':
		if(Ode_Controller::getInstance()->getAuth()->getSession()->type()->type_name == "admin") {
			switch(\Ode\Manager::getInstance()->getTask()) {
				default:
					$freqs = \Ode\DBO::getInstance()->query("
						SELECT freq.*
						FROM frequencies AS freq
						WHERE freq.is_active = 0
						ORDER BY freq.created
						DESC
					")->fetchAll(PDO::FETCH_CLASS, "Ode_DBO_Frequency_Model");
					
					//Util::debug($freqs);
					\Ode\View::getInstance()->assign("freqs", $freqs);
					break;
				case 'approve':
					$freq = \Ode\DBO::getInstance()->query("
						SELECT freq.*
						FROM frequencies AS freq
						WHERE freq.id = " . \Ode\DBO::getInstance()->quote($_GET['fid']) . "
						LIMIT 0,1
					")->fetchObject("Ode_DBO_Frequency_Model");
					\Ode\View::getInstance()->assign("freq", $freq);
					
					$groups = \Ode\DBO::getInstance()->query("
						SELECT `group`.*
						FROM group_county_cnx AS cnx
						LEFT JOIN groups AS `group` ON (`group`.id = cnx.group_id)
						WHERE cnx.county_id = " . \Ode\DBO::getInstance()->quote($_GET['cid'], PDO::PARAM_INT) . "
						AND group.is_active = 1
						ORDER BY `group`.title
						ASC 
					")->fetchAll(PDO::FETCH_CLASS, "Ode_DBO_Group_Model");
					
					\Ode\View::getInstance()->assign("groups", $groups);
					break;
				case 'select':
					$sth = \Ode\DBO::getInstance()->prepare("
						INSERT INTO frequency_group_cnx (frequency_id, group_id) 
						VALUES (:freq_id, :group_id)
					");
					$sth->bindValue(":freq_id", $_GET['freq_id'], PDO::PARAM_STR);
					$sth->bindValue(":group_id", $_GET['group_id'], PDO::PARAM_STR);
					
					try {
						$sth->execute();
						
						$sth = \Ode\DBO::getInstance()->prepare("
							UPDATE frequencies 
							SET 
								is_active = 1, 
								user_id = :user_id, 
								modified = NOW() 
							WHERE id = :id	
						");
						$sth->bindValue(":user_id", \Ode\Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
						$sth->bindValue(":id", $_GET['freq_id'], PDO::PARAM_STR);
						
						try {
							$sth->execute();
							
							header("Location: " . \Ode\Manager::getInstance()->action("frequencies", "admin"));
							exit();
						} catch(PDOException $e) {
							Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
						}
					} catch(PDOException $e) {
						Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
					}
					break;
			}
		}
		break;
}
$view->display("frequencies.tpl.php");
exit();
?>