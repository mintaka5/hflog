<?php
require_once './init.php';

switch (\Ode\Manager::getInstance()->getMode()) {
    default:
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:
                $logs = \Ode\DBO\Hflog::getAllActive(50);
                
                \Ode\Utils\Json::encode($logs);
                break;
        }
        break;
    case 'user':
        switch(\Ode\Manager::getInstance()->getTask()) {
            default:

                break;
            case 'location':
                $session = \Ode\Auth::getInstance()->getSession();

                if($session != false) {
                    Util::json(array('lat' => floatval($session->lat), 'lng' => floatval($session->lng)));
                } else {
                    Util::json(false);
                }

                exit();
                break;
        }
        break;
}

