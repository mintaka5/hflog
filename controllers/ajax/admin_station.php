<?php
require_once './init.php';

if (!\Ode\Auth::getInstance()->isAuth()) {
    Util::json(false, 'Unauthorized access');
    exit();
}

switch (\Ode\Manager::getInstance()->getMode()) {
    default:

        break;
    case 'add':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:
                $stationId = \Ode\DBO\SWStation::insertOne($_POST['name']);

                $newStation = \Ode\DBO\SWStation::getOneById($stationId);

                \Ode\Utils\Json::encode($newStation);
                break;
        }
        break;
    case 'update':
        switch(\Ode\Manager::getInstance()->getTask()) {
            default:
                /**
                 * @var \Ode\DBO\SWStation\Model
                 */
                $station = \Ode\DBO\SWStation::getOneByName($_REQUEST['name']);

                /**
                 * @var \Ode\DBO\SWStation\Entity
                 */
                $update = new \Ode\DBO\SWStation\Entity($station->id, $station->station_name, $_REQUEST['title'], $station->is_active, new DateTime('now'));

                \Ode\DBO\SWStation::update($update);

                \Ode\Utils\Json::encode($update);
                break;
        }
        break;
    case 'get':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default: break;
            case 'all':
                $stations = Ode\DBO\SWStation::getAll();

                \Ode\Utils\Json::encode($stations);
                break;
            case 'inactive':
                $stations = \Ode\DBO\SWStation::getInactive();

                \Ode\Utils\Json::encode($stations);
                break;
            case 'one':
                $station = \Ode\DBO\SWStation::getOneById($_POST['id']);

                \Ode\Utils\Json::encode($station);
                break;
        }
        break;
    case 'active':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default: break;
            case 'no':
                \Ode\DBO\SWStation::setActive($_POST['id'], 0);
                break;
            case 'yes':
                \Ode\DBO\SWStation::setActive($_POST['id'], 1);
                break;
        }
        break;
    case 'delete':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:
                \Ode\DBO\SWStation::delete($_POST['id']);
                break;
        }
        break;
}