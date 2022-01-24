<?php
ini_set("display_errors", "Off");

require_once './init.php';

switch(\Ode\Manager::getInstance()->getMode()) {
	default:
		
		break;
	case 'graph':
		switch(\Ode\Manager::getInstance()->getTask()) {
			default:
			case 'range':
				echo '<img src="' . \Ode\Manager::getInstance()->action("ham-radio", "graph", null, array("span", $_POST['spanStr'])) . '" alt="" />';
				exit();
				break;
		}
		break;
        case 'bot':
			//die();
            switch(\Ode\Manager::getInstance()->getTask()) {
                default: break;
                case 'search':
                	$sQry = trim($_POST['q']);
                	$qry = "%" . preg_replace("/[\s\t\r\n]+/", "%", $sQry) . "%";
                	
                	$sql = "SELECT " . \Ode\DBO\Hflog::COLUMNS . "
                                FROM " . \Ode\DBO\Hflog::TABLE_NAME . " AS a
                                LEFT JOIN hflog_sw_locations AS b ON (b.hflog_id = a.id)
                                LEFT JOIN sw_locations AS c ON (c.id = b.sw_loc_id)
                                LEFT JOIN sw_stations AS d ON (d.id = c.station_id)
                                WHERE a.description LIKE " . \Ode\DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
                                OR c.site LIKE " . \Ode\DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
                                OR d.title LIKE " . \Ode\DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
                                ORDER BY a.time_on
                                DESC
                                LIMIT 0,10";
                	//print $sql; die();
                	
                	try {
                		$logs = \Ode\DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, \Ode\DBO\Hflog::MODEL_NAME);
                	} catch(Exception $e) {
                		mail(
                                    "cjwalsh@ymail.com", 
                                    "SkyKing failed: " . __LINE__ . ", " . __FILE__, 
                                    $e->getMessage(),
                                    "From: no-reply@qualsh.com\r\nReply-To: no-reply@qualsh.com\r\nX-Mailer: PHP/" . phpversion()
                		);
                		
                		Util::json($e->getMessage());
                		exit();
                	}
                    
                	$ary = ircLogs($logs);
                    Util::json($ary);
                    exit();
                	break;
                case 'log':
                    $username = trim($_POST['user']);
                    $hostmask = trim($_POST['host']);
                    
                    $isRegistered = Ode_DBO_User_IRC::isRegistered($username, $hostmask);
                    
                    if($isRegistered != false) {
                        $frequency = trim($_POST['freq']);
                        $mode = trim($_POST['mode']);
                        $mode = strtoupper($mode);
                        $desc = trim($_POST['desc']);
                        
                        $logId = \Ode\DBO::getInstance()->query("SELECT UUID()")->fetchColumn();
                        
                        $sth = \Ode\DBO::getInstance()->prepare("
                            INSERT INTO " . \Ode\DBO\Hflog::TABLE_NAME . " (id, frequency, mode, description, time_on, lat, lng, user_id, submitted)
                            VALUES (:id, :freq, :mode, :desc, UTC_TIMESTAMP(), 0.0, 0.0, :user, UTC_TIMESTAMP())
                        ");
                        $sth->bindValue(":id", $logId, PDO::PARAM_STR);
                        $sth->bindValue(":freq", $frequency, PDO::PARAM_INT);
                        $sth->bindValue(":mode", $mode, PDO::PARAM_STR);
                        $sth->bindValue(":desc", $desc, PDO::PARAM_STR);
                        $sth->bindValue(":user", $isRegistered->id, PDO::PARAM_STR);
                        
                        try {
                            $sth->execute();
                        } catch(Exception $e) {
                            Ode_Log::getInstance()->log($e->getMessage(), E_USER_ERROR);
                        }
                    } else {
                        Util::json(false);
                        exit();
                    }
                    
                    Util::json(array("postdata" => $_POST, "timestamp" => gmdate("Y-m-d H:i"), "log_id" => $logId));
                    break;
                case 'register':
                    $hostmask = strval(trim($_POST['host']));
                    $username = strval(trim($_POST['uname']));
                    $email = strval(trim($_POST['email']));
                    $firstname = strval(trim($_POST['fname']));
                    $lastname = strval(trim($_POST['lname']));
                    $passwd = strval(trim($_POST['pass']));
                    $lat = trim($_POST['lat']);
                    $lng = trim($_POST['lng']);
                    
                    $isRegistered = Ode_DBO_User_IRC::isRegistered($username, $hostmask);
                    
                    /**
                     * already registered
                     */
                    if($isRegistered != false) {
                        Util::json(false);
                        exit();
                    }
                    
                    \Ode\DBO::getInstance()->beginTransaction();
                    
                    $uuid = \Ode\DBO::getInstance()->query("SELECT UUID()")->fetchColumn();
                    
                    $sth = \Ode\DBO::getInstance()->prepare("
                        INSERT INTO " . \Ode\DBO\User::TABLE_NAME . " (id, username, email, password, firstname, lastname, lat, lng, created, modified)
                        VALUES (:id, :uname, :email, MD5(:pass), :fname, :lname, :lat, :lng, NOW(), NOW())
                    ");
                    $sth->bindValue(":id", $uuid, PDO::PARAM_STR);
                    $sth->bindValue(":uname", $username, PDO::PARAM_STR);
                    $sth->bindValue(":email", $email, PDO::PARAM_STR);
                    $sth->bindValue(":pass", $passwd, PDO::PARAM_STR);
                    $sth->bindValue(":fname", $firstname, PDO::PARAM_STR);
                    $sth->bindValue(":lname", $lastname, PDO::PARAM_STR);
                    $sth->bindValue(":lat", $lat, PDO::PARAM_STR);
                    $sth->bindValue(":lng", $lng, PDO::PARAM_STR);
                    
                    try {
                        $sth->execute();
                    } catch(Exception $e) {
                        Ode_Log::getInstance()->log($e->getMessage(), E_USER_ERROR);
                    }
                    
                    $sth = \Ode\DBO::getInstance()->prepare("
                        INSERT INTO " . Ode_DBO_User_IRC::TABLE_NAME . " (user_id, hostmask)
                        VALUES (:user, :host)
                    ");
                    $sth->bindValue(":user", $uuid, PDO::PARAM_STR);
                    $sth->bindValue(":host", $hostmask, PDO::PARAM_STR);
                    
                    try {
                        $sth->execute();
                    } catch(Exception $e) {
                        Ode_Log::getInstance()->log($e->getMessage());
                    }
                    
                    \Ode\DBO::getInstance()->commit();
                    
                    unset($_POST['pass']);
                    Util::json($_POST);
                    break;
                case 'freq':
                    $freq = (float)trim($_POST['freq']);
                    
                    $sql = "SELECT " . \Ode\DBO\Hflog::COLUMNS . "
                        FROM " . \Ode\DBO\Hflog::TABLE_NAME . " AS a
                        WHERE a.frequency
                        BETWEEN (" . $freq . " - 0.5)
                        AND (" . $freq . " + 0.5)
                        ORDER BY a.time_on
                        DESC
                    	LIMIT 0,10";
                    //echo $sql;
                    $logs = \Ode\DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, \Ode\DBO\Hflog::MODEL_NAME);
                    
                    $ary = array();
                    
                    $logAry = new ArrayObject($logs);
                    $iter = $logAry->getIterator();
                    
                    if($iter->count() > 0) {
                        while($iter->valid()) {
                            $ary[] = array(
                                'frequency' => number_format($iter->current()->frequency, 2, '.', '')." kHz",
                                'time_on' => date("M d, Y H:i", strtotime($iter->current()->time_on)),
                                'mode' => $iter->current()->mode,
                                'desc' => $iter->current()->description(),
                                'location' => ($iter->current()->hasLocation() == true) ? $iter->current()->location()->location()->site : "",
                                'station' => ($iter->current()->hasLocation() == true) ? $iter->current()->location()->location()->station()->title : ""
                            );

                            $iter->next();
                        }
                    } else {
                        $ary = false;
                    }
                    
                    Util::json($ary, true);
                    exit();
                    break;
                case 'tsearch':
                	$sQry = trim($_POST['terms']);
                        $qry = "%" . preg_replace("/[\s\t\r\n]+/", "%", $sQry) . "%";
                        //echo $qry; die();

                        $sql = "SELECT " . \Ode\DBO\Hflog::COLUMNS . "
                                        FROM " . \Ode\DBO\Hflog::TABLE_NAME . " AS a
                                        LEFT JOIN hflog_sw_locations AS b ON (b.hflog_id = a.id)
                                        LEFT JOIN sw_locations AS c ON (c.id = b.sw_loc_id)
                                        LEFT JOIN sw_stations AS d ON (d.id = c.station_id)
                                        WHERE (
                                            a.frequency = " . \Ode\DBO::getInstance()->quote($sQry, PDO::PARAM_INT) . "
                                            OR a.description LIKE " . \Ode\DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
                                            OR c.site LIKE " . \Ode\DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
                                            OR d.title LIKE " . \Ode\DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
                                        )
                                        ORDER BY a.submitted
                                        DESC
                                        LIMIT 0,10";

                        $logs = \Ode\DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, \Ode\DBO\Hflog::MODEL_NAME);

                        $ary = array();

                        $logAry = new ArrayObject($logs);
                        $iter = $logAry->getIterator();

                        while($iter->valid()) {
                                $ary[] = array(
                                                'frequency' => number_format($iter->current()->frequency, 2, '.', '')." kHz",
                                                'time_on' => date("M d, Y H:i", strtotime($iter->current()->time_on)),
                                                'mode' => $iter->current()->mode,
                                                'desc' => $iter->current()->description(),
                                                'location' => ($iter->current()->hasLocation() == true) ? $iter->current()->location()->location()->site : "",
                                                'station' => ($iter->current()->hasLocation() == true) ? $iter->current()->location()->location()->station()->title : ""
                                );

                                $iter->next();
                        }

                        Util::json($ary, true);
                        exit();
                	break;
                case 'cur_condx':
                    $report = \Ode\DBO::getInstance()->query("
                        SELECT a.*
                        FROM propagation AS a
                        ORDER BY a.id
                        DESC
                        LIMIT 0,1
                    ")->fetchObject();

                    /*header("Content-Type: text/plain");
                    echo "Solar flux: ".$report->solar_flux.
                            "; A Index: ".$report->a_index.
                            "; K Index: ".$report->k_index.
                            "; Sun spots: ".$report->sun_spots.
                            "; Report time: ".date("M d, Y H:i", strtotime($report->reported))." UTC";*/
                    Util::json($report);
                    exit();
                    break;
                case 'bands':
                    $cdxColorMap = new ArrayObject();
                    $cdxColorMap->offsetSet("poor", "red");
                    $cdxColorMap->offsetSet("fair", "yellow");
                    $cdxColorMap->offsetSet("good", "green");
                    
                    $req = new HTTP_Request2("http://www.hamqsl.com/solarxml.php", HTTP_Request2::METHOD_GET);
	
                    if($req->send()) {
						$xmlStr = $req->send()->getBody();
						
						$doc = new DOMDocument("1.0", "UTF-8");
						$doc->loadXML($xmlStr);
						
						$xpath = new DOMXPath($doc);
			                        
			                        $cdxs = $xpath->query("/solar/solardata/calculatedconditions/band");
			                        
			                        $cdxAry = array();
						foreach($cdxs as $cdx) {
							$timeOfDay = trim($xpath->query("@time", $cdx)->item(0)->textContent);
							$band = trim($xpath->query("@name", $cdx)->item(0)->textContent);
							$condition = trim($xpath->query(".", $cdx)->item(0)->textContent);
							
							$cdxAry[$band][$timeOfDay] = array('color' => $cdxColorMap->offsetGet(strtolower($condition)), 'condition' => $condition);
						}
                        
                        Util::json($cdxAry);
                        exit();
                    } else {}
                    break;
                case 'update_mask':
                	$user = \Ode\DBO::getInstance()->query("
                		SELECT a.id
                		FROM " . \Ode\DBO\User::TABLE_NAME . " AS a
                		LEFT JOIN " . Ode_DBO_User_IRC::TABLE_NAME . " AS b ON (b.user_id = a.id)
                		WHERE a.username = " . \Ode\DBO::getInstance()->quote($_POST['user']) . "
                		AND a.password = MD5(" . \Ode\DBO::getInstance()->quote($_POST['pass']) . ")
                		AND b.user_id = a.id
                	")->fetchColumn();
                	
                	if($user != false) {
                		$sth = \Ode\DBO::getInstance()->prepare("
                			UPDATE " . Ode_DBO_User_IRC::TABLE_NAME . "
                			SET
                				hostmask = :hostmask
                			WHERE user_id = :user
                		");
                		$sth->bindValue(":hostmask", $_POST['mask'], PDO::PARAM_STR);
                		$sth->bindValue(":user", $user, PDO::PARAM_STR);
                		
                		try {
                			$sth->execute();
                		} catch(Exception $e) {
                			Ode_Log::getInstance()->log($e->getMessage(), E_USER_ERROR);
                		}
                		
                		Util::json(true);
                	} else {
                		Util::json(false);
                	}
                	
                	exit();
                	break;
                case 'now':
                	$logs = \Ode\DBO::getInstance()->query("SELECT " . \Ode\DBO\Hflog::COLUMNS . "
                            FROM " . \Ode\DBO\Hflog::TABLE_NAME . " AS a
                            WHERE (HOUR(time_on) = hour(UTC_TIMESTAMP())
                            AND DAYOFWEEK(time_on) = DAYOFWEEK(UTC_TIMESTAMP()))
                            OR (HOUR(time_off) = HOUR(UTC_TIMESTAMP())
                            AND DAYOFWEEK(time_off) = DAYOFWEEK(UTC_TIMESTAMP()))
                            ORDER BY a.time_on
                            DESC
                            LIMIT 0,10
                        ")->fetchAll(PDO::FETCH_CLASS, \Ode\DBO\Hflog::MODEL_NAME);
                	
                	$log_list = ircLogs($logs);
                	
                	Util::json($log_list);
                	exit();
                	break;
                case 'anyday':
                	$offset = (!isset($_POST['offset'])) ? 1 : intval($_POST['offset']);
                	$offset = ($offset < 0) ? 1 : $offset;
                	$offset = $offset - 1;
                	$offset = ($offset % 10 != 0) ? $offset + 10 - ($offset % 10) : $offset;
                	
                	$logs = \Ode\DBO::getInstance()->query("
                		SELECT " . \Ode\DBO\Hflog::COLUMNS . "
                		FROM " . \Ode\DBO\Hflog::TABLE_NAME . " AS a
                		WHERE HOUR(time_on) = HOUR(UTC_TIMESTAMP())
                		OR HOUR(time_off) = HOUR(UTC_TIMESTAMP())
                		ORDER BY a.time_on
                		ASC
                		LIMIT 0,10
                	")->fetchAll(PDO::FETCH_CLASS, \Ode\DBO\Hflog::MODEL_NAME);
                	
                	$log_list = ircLogs($logs);
                	
                	Util::json($log_list);
                	exit();
                	break;
                case 'recent':
                	$logs = \Ode\DBO::getInstance()->query("
                		SELECT " . \Ode\DBO\Hflog::COLUMNS . "
						FROM " . \Ode\DBO\Hflog::TABLE_NAME . " AS a
						WHERE a.time_on BETWEEN DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 WEEK) AND UTC_TIMESTAMP() 
						ORDER BY a.submitted
						DESC
						LIMIT 0,10
					")->fetchAll(PDO::FETCH_CLASS, \Ode\DBO\Hflog::MODEL_NAME);
                	
                	$log_list = ircLogs($logs);
                	 
                	Util::json($log_list);
                	exit();
                	break;
            }
}

/**
 * converts DBO object to object ready for IRC listing
 * @param array:Ode_DBO_Hflog_Model
 */
function ircLogs($logs) {
	$ary = array();
	
	$logAry = new ArrayObject($logs);
	$iter = $logAry->getIterator();
	
	while($iter->valid()) {
		$ary[] = array(
				'frequency' => number_format($iter->current()->frequency, 2, '.', '')." kHz",
				'time_on' => date("M d, Y H:i", strtotime($iter->current()->time_on)),
				'mode' => $iter->current()->mode,
				'desc' => $iter->current()->description(),
				'location' => ($iter->current()->hasLocation() == true) ? $iter->current()->location()->location()->site : "",
				'station' => ($iter->current()->hasLocation() == true) ? $iter->current()->location()->location()->station()->title : ""
		);
	
		$iter->next();
	}
	
	return $ary;
}
?>