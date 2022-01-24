<?php
if(!\Ode\Auth::getInstance()->isAdmin()) {
    header("Location: " . \Ode\Manager::getInstance()->friendlyAction("auth"));
    exit();
}


switch(\Ode\Manager::getInstance()->getMode()) {
    default:
        switch(\Ode\Manager::getInstance()->getTask()) {
            default:
                
                break;
        }
        break;
    case 'audio':
        switch(\Ode\Manager::getInstance()->getTask()) {
            default: break;
            case 'delete':
                \Ode\DBO\Hflog\Audio\Cnx::deleteAllByLog($_GET['id']);

                header('Location: ' . \Ode\Manager::getInstance()->friendlyAction('logs', 'log', 'edit', array('id', $_GET['id'])));
                exit();
                break;
        }
        break;
    case 'log':
        switch(\Ode\Manager::getInstance()->getTask()) {
            default:

                break;
            case 'approve':
                $logId = strval(trim($_GET['id']));

                \Ode\DBO\Hflog::approve($logId);
                
                header('Location: ' . \Ode\Manager::getInstance()->action('admin_hflogs'));
                exit();
                break;
            case 'unapprove':
                $logId = strval(trim($_GET['id']));

                \Ode\DBO\Hflog::unapprove($logId);

                header('Location: ' . \Ode\Manager::getInstance()->action('admin_hflogs'));
                exit();
                break;
            case 'delete':
                $logId = strval(trim($_GET['id']));

                \Ode\DBO\Hflog::delete($logId);
                
                header('Location: ' . \Ode\Manager::getInstance()->action('admin_hflogs'));
                exit();
                break;
        }
        break;
    case 'inactive':
        switch(\Ode\Manager::getInstance()->getTask()) {
            default:
                $logs = \Ode\DBO\Hflog::getAllInactive();

                \Ode\View::getInstance()->assign('logs', $logs);
                break;
        }
        break;
    case 'process':
        switch(\Ode\Manager::getInstance()->getTask()) {
            default:
                if(!empty($_POST['log'])) {
                    $logs = $_POST['log'];
                    foreach($logs as $logId) {
                        switch($_POST['act']) {
                            case \Ode\DBO\Hflog\Status::STATUS_APPROVED:
                                \Ode\DBO\Hflog::approve($logId);
                                break;
                            case \Ode\DBO\Hflog\Status::STATUS_NOT_APPROVED:
                                \Ode\DBO\Hflog::unapprove($logId);
                                break;
                            case 'delete':
                                \Ode\DBO\Hflog::delete($logId);
                                break;
                        }
                    }
                }
                
                header('Location: ' . \Ode\Manager::getInstance()->action('admin_hflogs'));
                exit();
                break;
        }
        break;
    case 'unapproved':
        switch(\Ode\Manager::getInstance()->getTask()) {
            default:
                $logs = \Ode\DBO\Hflog::getAllNotApproved();

                \Ode\View::getInstance()->assign('logs', $logs);
                break;
        }
        break;
    case 'stn':
        switch(\Ode\Manager::getInstance()->getTask()) {
            default: break;
            case 'view':
                $station = \Ode\DBO\SWStation::getOneById(trim($_GET['id']));
                
                \Ode\View::getInstance()->assign("station", $station);
                break;
        }
        break;
    case 'loc':
        switch(\Ode\Manager::getInstance()->getTask()) {
            default:
                
                break;
            case 'set':
            	$log = \Ode\DBO\Hflog::getOneById(trim($_GET['id']));
            	
            	$form = new HTML_QuickForm2("addLocForm");
            	
            	$form->setAttribute("action", \Ode\Manager::getInstance()->action("admin_hflogs", "loc", "set", array("id", $log->id), array("page", trim($_GET['page']))));
                $form->setAttribute('role', 'form');
                
                $form->addHidden('logid')->setValue($log->id);
            	
            	$stnSel = $form->addSelect("stn")->setLabel("Station")->setAttribute('class', 'form-control');
            	$stnSel->addOption("- select -", "");
            	$stnSel->addRule("required", "Required");
            	
                $stations = \Ode\DBO\SWStation::getAll();
                foreach($stations as $stn) {
                    $stnSel->addOption($stn->title, $stn->id);
                }

                $locSel = $form->addSelect("loc")->setLabel("Location")->setAttribute('class', 'form-control');

                $languages = \Ode\DBO\Language::getAll();

                $langSel = $form->addSelect('lang')->setLabel('Language')->setAttribute('class', 'form-control');
                $langSel->addOption('- none -', '');
                foreach($languages as $lang) {
                    $langSel->addOption($lang->language, $lang->iso);
                }
                
                if($log->hasLocation()) {
                    $stnSel->setValue($log->location()->location()->station()->id);
                    
                    $stnLocs = \Ode\DBO\SWLocation::getAllByStation($log->location()->location()->station()->id);
                    foreach($stnLocs as $stnLoc) {
                        $locSel->addOption($stnLoc->site . ': ' . $stnLoc->times() . ' - ' . $stnLoc->frequency(), $stnLoc->id);
                    }
                    
                    $locSel->setValue($log->location()->location()->id);
                }

                $form->addButton('submitBtn')->setContent('Set')->setAttribute('class', 'btn btn-default');

                if($form->validate()) {
                    \Ode\DBO\Hflog\SWLocation::connect($_POST['loc'], $log->id);
                    if(!empty($_POST['lang'])) {
                        \Ode\DBO\SWLocation::setLanguage($_POST['loc'], $_POST['lang']);
                    }

                    header("Location: " . \Ode\Manager::getInstance()->friendlyAction("logs", null, null, array("page", trim($_GET['page']))));
                    exit();
                }

                \Ode\View::getInstance()->assign("form", $form->render(\Ode\View::getInstance()->getFormRenderer()));
                \Ode\View::getInstance()->assign("log", $log);
            	break;
            case 'add':
                $station = \Ode\DBO\SWStation::getOneById(trim($_GET['id']));
                
                $form = new \HTML_QuickForm2("addLocation");
                $form->setAttribute("action", \Ode\Manager::getInstance()->friendlyAction("admin_hflogs", "loc", "add", array("id", $station->id)));
                
                $latTxt = $form->addText("lat")->setLabel("Latitude");
                $lngTxt = $form->addText("lng")->setLabel("Longitude");
                
                $siteTxt = $form->addText("site")->setLabel("Site description");
                $siteTxt->addRule("required", "Required");
                
                $startText = $form->addText("startUTC")->setLabel("Start UTC");
                $endText = $form->addText("endUTC")->setLabel("End UTC");
                $startText->addRule("regex", "Must be 00:00 format", "/\d{2}\:\d{2}/");
                
                $freqTxt = $form->addText("freq")->setLabel("Frequency");
                
                $powerTxt = $form->addText("pwr")->setLabel("Power");
                
                $langSel = $form->addSelect("lang")->setLabel("Language");
                $langSel->addOption("n/a", "");
                $langs = \Ode\DBO\Language::getAll();
                foreach($langs as $lang) {
                    $langSel->addOption($lang->language, $lang->iso);
                }
                
                $form->addSubmit("submitBtn")->setValue("Add");
                
                if($form->validate()) {
                    $sth = \Ode\DBO::getInstance()->prepare("
                        INSERT INTO " . \Ode\DBO\SWLocation::TABLE_NAME . " (station_id, lat, lng, site, start_utc, end_utc, frequency, power, lang_iso)
                        VALUES (
                            :station, :lat, :lng, :site, :start, :end,
                            :freq, :power, :lang
                        )    
                    ");
                    $sth->bindValue(":station", $station->id, \PDO::PARAM_STR);
                    $sth->bindValue(":lat", trim($_POST['lat']), \PDO::PARAM_INT);
                    $sth->bindValue(":lng", trim($_POST['lng']), \PDO::PARAM_INT);
                    $sth->bindValue(":site", trim($_POST['site']), \PDO::PARAM_STR);
                    $sth->bindValue(":start", trim($_POST['startUTC']), \PDO::PARAM_STR);
                    $sth->bindValue(":end", trim($_POST['endUTC']), \PDO::PARAM_STR);
                    $sth->bindValue(":freq", trim($_POST['freq']), \PDO::PARAM_INT);
                    $sth->bindValue(":power", trim($_POST['pwr']), \PDO::PARAM_INT);
                    $sth->bindValue(":lang", $_POST['lang'], \PDO::PARAM_STR);
                    
                    try {
                        $sth->execute();
                    } catch(\Exception $e) {
                        
                    }
                    
                    header("Location: " . \Ode\Manager::getInstance()->friendlyAction("admin_hflogs", "stn", "view", array("id", $station->id)));
                    exit();
                }
                
                $renderer = \HTML_QuickForm2_Renderer::factory("default");
		        \Ode\View::getInstance()->assign("form", $form->render($renderer));
                break;
        }
        break;
}
