<?php
require_once './init.php';

if(!\Ode\Auth::getInstance()->isAuth()) {
    Util::json(false, 'Unauthorized access');
    exit();
}

switch(\Ode\Manager::getInstance()->getMode()) {
    default:

        break;
    case 'validate':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:
                break;
            case 'coordinates':
                $lat = $_POST['locLat'];
                $lng = $_POST['locLong'];

                $address = \Ode\Geo\Google\Geocode::fromLatLng(new \Ode\Geo\LatLng($lat, $lng));

                \Ode\Utils\Json::encode($address);
                break;
        }
        break;
    case 'update':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:
                $data = $_POST;
                $data = $data['data'];

                $station = \Ode\DBO\SWStation::getOneById($data['station']['id']);
                $coords = new \Ode\Geo\LatLng(floatval($data['coordinates']['lat']), floatval($data['coordinates']['lng']));

                $startTime = \Ode\Utils\Time::dateTimeFromTime($data['start_utc'], 'UTC');
                $endTime = \Ode\Utils\Time::dateTimeFromTime($data['end_utc'], 'UTC');

                $langIso = \Ode\DBO\Language::getOneByISO($data['language']['iso']);
                if(empty($langIso)) {
                    $langIso = null;
                }

                $location = new \Ode\DBO\SWLocation\Entity(
                    $data['id'],
                    $station,
                    $coords,
                    null,
                    $data['site'],
                    $startTime,
                    $endTime,
                    null,
                    $data['frequency'],
                    null,
                    $langIso
                );
                \Ode\DBO\SWLocation::updateOne($location);

                \Ode\Utils\Json::encode($data);
                break;
        }
        break;
    case 'add':
        switch(\Ode\Manager::getInstance()->getTask()) {
            default:
                $data = $_REQUEST;
                $data = $data['data'];

                /**
                 * @var \Ode\DBO\SWStation\Model
                 */
                $station = \Ode\DBO\SWStation::getOneByName($data['locStnName']);

                $coords = new \Ode\Geo\LatLng(floatval($data['locLat']), floatval($data['locLong']));

                $startTime = \Ode\Utils\Time::dateTimeFromTime($data['locStart'] . ':00', 'UTC');
                $endTime = \Ode\Utils\Time::dateTimeFromTime($data['locEnd'] . ':00', 'UTC');
                
                $langIso = \Ode\DBO\Language::getOneByISO($data['locLangIso']);
                if(empty($langIso)) {
                    $langIso = null;
                }

                $location = new \Ode\DBO\SWLocation\Entity(
                    null,
                    $station,
                    $coords,
                    null,
                    $data['locSite'],
                    $startTime,
                    $endTime,
                    null,
                    $data['locFreq'],
                    null,
                    $langIso
                );
                $newLocId = \Ode\DBO\SWLocation::insertOne($location);

                \Ode\Utils\Json::encode(array(
                    'locationId' => $newLocId,
                    'submittedData' => $data
                ));
                break;
            case 'admin':
                $data = $_POST;
                $data = $data['data'];

                $station = \Ode\DBO\SWStation::getOneById($data['station']['id']);
                $coords = new \Ode\Geo\LatLng(floatval($data['coordinates']['lat']), floatval($data['coordinates']['lng']));

                $startTime = \Ode\Utils\Time::dateTimeFromTime($data['start_utc'] . ':00', 'UTC');
                $endTime = \Ode\Utils\Time::dateTimeFromTime($data['end_utc'] . ':00', UTC);

                $langIso = \Ode\DBO\Language::getOneByISO($data['language']['iso']);
                if(empty($langIso)) {
                    $langIso = null;
                }

                $location = new \Ode\DBO\SWLocation\Entity(
                    null,
                    $station,
                    $coords,
                    null,
                    $data['site'],
                    $startTime,
                    $endTime,
                    null,
                    $data['frequency'],
                    null,
                    $langIso
                );
                $newLocId = \Ode\DBO\SWLocation::insertOne($location);

                \Ode\Utils\Json::encode($data);
                break;
        }
        break;
    case 'get':
        switch(\Ode\Manager::getInstance()->getTask()) {
            default:

                break;
            case 'all':
                $locations = \Ode\DBO\SWLocation::getAll();

                \Ode\Utils\Json::encode($locations);
                break;
            case 'bystation':
                $locations = \Ode\DBO\SWLocation::getAllByStation($_POST['id']);

                \Ode\Utils\Json::encode($locations);
                break;
            case 'inactive':
                $locations = \Ode\DBO\SWLocation::getInactive();

                \Ode\Utils\Json::encode($locations);
                break;
        }
        break;
    case 'active':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default: break;
            case 'yes':
                \Ode\DBO\SWLocation::setActive($_POST['id'], 1);
                break;
            case 'no':
                \Ode\DBO\SWLocation::setActive($_POST['id'], 0);
                break;
        }
        break;
    case 'delete':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:
                \Ode\DBO\SWLocation::delete($_POST['id']);
                break;
        }
        break;
}