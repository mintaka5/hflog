<?php
require_once './init.php';

switch (\Ode\Manager::getInstance()->getMode()) {
    default:
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:

                break;
        }
        break;
    case 'log':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:
                break;
            case 'infowin':
                $log_id = $_POST['log_id'];

                $log = \Ode\DBO\Hflog::getOneById($log_id);

                \Ode\View::getInstance()->assign("log", $log);

                echo \Ode\View::getInstance()->fetch("hflogs/map_infowin.tpl.php");
                exit();
                break;
            case 'json':
                $logId = $_POST['id'];
                $log = \Ode\DBO\Hflog::toJSON(\Ode\DBO\Hflog::getOneById($logId));

                echo $log;
                exit();
                break;
            case 'whatson':
                $logs = \Ode\DBO\Hflog::getWhatsOnNow();

                \Ode\Utils\Json::encode($logs);
                break;
            case 'recents':
                $logs = \Ode\DBO\Hflog::getAllActive(5);

                \Ode\Utils\Json::encode($logs);
                break;
            case 'search':
                $sQry = trim(strip_tags($_GET['q']));
                $qry = "%" . preg_replace("/[\s\t\r\n]+/", "%", $sQry) . "%";

                $sql = "SELECT " . \Ode\DBO\Hflog::COLUMNS . "
                        FROM " . \Ode\DBO\Hflog::TABLE_NAME . " AS a
                        WHERE a.frequency = " . \Ode\DBO::getInstance()->quote($sQry, \PDO::PARAM_INT) . "          
                        AND a.id NOT IN (
                            SELECT hflog_id 
                            FROM " . \Ode\DBO\Hflog\Status::TABLE_NAME . "
                            WHERE status IN (
                                        " . \Ode\DBO::getInstance()->quote(\Ode\DBO\Hflog\Status::STATUS_INACTIVE) . ",
                                        " . \Ode\DBO::getInstance()->quote(\Ode\DBO\Hflog\Status::STATUS_NOT_APPROVED) . "
                                    )
                        )
                        ORDER BY a.submitted
                        DESC
                        LIMIT 0,5";

                $logs = \Ode\DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, \Ode\DBO\Hflog::MODEL_NAME);

                \Ode\Utils\Json::encode($logs);
                break;
        }
        break;
    case 'lb':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:

                break;
            case 'alllogs':
                $logs = \Ode\DBO::getInstance()->query("
    				SELECT a.*
    				FROM " . \Ode\DBO\Hflog::TABLE_NAME . " AS a
    				ORDER BY a.time_on
    				DESC
    			")->fetchAll(PDO::FETCH_ASSOC);

                $json = new Services_JSON();
                echo $json->encode($logs);
                exit();
                break;
        }
        break;
    case 'stn':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:
                break;
            case 'add':
                $uuid = \Ode\DBO::getInstance()->query("SELECT UUID()")->fetchColumn();

                $sth = \Ode\DBO::getInstance()->prepare("
    				INSERT INTO " . \Ode\DBO\SWStation::TABLE_NAME . " (id, station_name, title)
    				VALUES (:id, :name, :title)
    			");
                $sth->bindValue(":id", $uuid, PDO::PARAM_STR);
                $sth->bindValue(":name", trim($_POST['name']), PDO::PARAM_STR);
                $sth->bindValue(":title", trim($_POST['title']), PDO::PARAM_STR);

                try {
                    $sth->execute();
                } catch (\Exception $e) {
                    error_log($e->getTraceAsString(), 0);
                }

                Util::json($uuid);
                exit();

                break;
            case 'locs':
                $stationId = trim($_POST['stationId']);

                $station = Ode_DBO_SWStation::getOneById($stationId);

                \Ode\View::getInstance()->assign("station", $station);

                echo \Ode\View::getInstance()->fetch("ajax/admin_location_list.php");

                exit();
                break;
        }
        break;
    case 'loc':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:
                break;
            case 'del':
                $log_id = $_POST['id'];

                \Ode\DBO\Hflog\SWLocation::delete($log_id);

                Util::json($_POST);
                exit();
                break;
            case 'add':
                \Ode\DBO::getInstance()->beginTransaction();

                $sth = \Ode\DBO::getInstance()->prepare("
    				INSERT INTO " . \Ode\DBO\SWLocation::TABLE_NAME . " (station_id, lat, lng, site, start_utc, end_utc, frequency)
    				VALUES (:stn, :lat, :lng, :site, :start, :end, :freq)
    			");
                $sth->bindValue(":stn", $_POST['stn'], \PDO::PARAM_STR);
                $sth->bindValue(":lat", floatval(trim($_POST['lat'])), \PDO::PARAM_INT);
                $sth->bindValue(":lng", floatval(trim($_POST['lng'])), \PDO::PARAM_INT);
                $sth->bindValue(":site", trim($_POST['site']), \PDO::PARAM_STR);
                $sth->bindValue(":start", trim($_POST['start']), \PDO::PARAM_STR);
                $sth->bindValue(":end", trim($_POST['end']), \PDO::PARAM_STR);
                $sth->bindValue(":freq", floatval(trim($_POST['freq'])), \PDO::PARAM_STR);

                try {
                    $sth->execute();
                } catch (\Exception $e) {
                    error_log($e->getTraceAsString(), 0);
                }

                $newId = \Ode\DBO::getInstance()->query("SELECT LAST_INSERT_ID()")->fetchColumn();

                \Ode\DBO::getInstance()->commit();

                Util::json($newId);
                exit();

                break;
        }
        break;
}
