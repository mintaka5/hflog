<?php
require_once './init.php';

switch (\Ode\Manager::getInstance()->getMode()) {
    default:
        $searchStr = "%" . preg_replace("/[\s\t\r\n\W]/", "%", trim($_POST['term'])) . "%";

        $sql = "
                SELECT 
                    a.id AS loc_id, a.lat, a.lng, a.site, a.start_utc, a.end_utc, ROUND(a.frequency, 2) AS frequency,
                    b.id AS station_id, IFNULL(b.title, '') AS title,
                    c.language
                FROM sw_locations AS a
                LEFT JOIN sw_stations AS b ON (b.id = a.station_id)
                LEFT JOIN languages AS c ON (c.iso = a.lang_iso)
                WHERE b.title LIKE " . \Ode\DBO::getInstance()->quote($searchStr, \PDO::PARAM_STR) . "
                OR a.site LIKE " . \Ode\DBO::getInstance()->quote($searchStr, \PDO::PARAM_STR) . "
                ORDER BY b.title
                ASC
                LIMIT 0,20";

        $locations = \Ode\DBO::getInstance()->query($sql)->fetchAll(\PDO::FETCH_OBJ);

        Util::json($locations);
        exit();
        break;
    case 'search':
        switch(\Ode\Manager::getInstance()->getTask()) {
            default:

                break;
            case 'by_stn':
                $station = \Ode\DBO\SWStation::getOneByName($_POST['stn_name']);

                $searchStr = "%" . preg_replace("/[\s\t\r\n\W]/", "%", trim($_POST['q'])) . "%";

                $sql = "
                SELECT 
                    a.id AS loc_id, a.lat, a.lng, a.site, a.start_utc, a.end_utc, ROUND(a.frequency, 2) AS frequency,
                    b.id AS station_id, IFNULL(b.title, '') AS title,
                    c.language
                FROM sw_locations AS a
                LEFT JOIN sw_stations AS b ON (b.id = a.station_id)
                LEFT JOIN languages AS c ON (c.iso = a.lang_iso)
                WHERE a.station_id = " . \Ode\DBO::getInstance()->quote($station->id, PDO::PARAM_STR) . "
                AND (b.title LIKE " . \Ode\DBO::getInstance()->quote($searchStr, \PDO::PARAM_STR) . "
                OR a.site LIKE " . \Ode\DBO::getInstance()->quote($searchStr, \PDO::PARAM_STR) . ")
                ORDER BY b.title
                ASC
                LIMIT 0,50";

                $locations = \Ode\DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_OBJ);

                \Ode\Utils\Json::encode($locations);
                break;
        }
        break;
    case 'by_stn':
        $locs = \Ode\DBO\SWLocation::getAllByStation(\Ode\DBO\SWStation::getIdByName($_POST['stn_id']));

        \Ode\Utils\Json::encode($locs);
        break;
    case 'by_id':
        $location = \Ode\DBO\SWLocation::getOneById($_POST['id']);

        Util::json($location);
        exit();
        break;
}
