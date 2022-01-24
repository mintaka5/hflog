<?php
require_once './init.php';

switch (\Ode\Manager::getInstance()->getMode()) {
    default:
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
                $sth->bindValue(":id", $uuid, \PDO::PARAM_STR);
                $sth->bindValue(":name", trim($_POST['name']), \PDO::PARAM_STR);
                $sth->bindValue(":title", trim($_POST['title']), \PDO::PARAM_STR);

                try {
                    $sth->execute();
                } catch (\Exception $e) {
                    error_log($e->getTraceAsString(), 0);
                }

                Util::json($uuid);
                exit();
                break;
        }
        break;
    case 'loc':
        switch(\Ode\Manager::getInstance()->getTask()) {
            default:

                break;
            case 'del':
                $log_id = $_POST['id'];

                Ode_DBO_Hflog_SWLocation::delete($log_id);

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
                } catch(\Exception $e) {
                    error_log($e->getTraceAsString(), 0);
                }

                $newId = \Ode\DBO::getInstance()->query("SELECT LAST_INSERT_ID()")->fetchColumn();

                \Ode\DBO::getInstance()->commit();

                Util::json($newId);
                exit();
                break;
        }
        break;
    case 'search':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:
                break;
            case 'all':
                $sQry = trim($_GET['_terms']);
                $qry = "%" . preg_replace("/[\s\t\r\n]+/", "%", $sQry) . "%";

                $sql = "SELECT a.*
						FROM " . \Ode\DBO\Hflog::TABLE_NAME . " AS a
						LEFT JOIN " . Ode_DBO_Hflog_SWLocation::TABLE_NAME . " AS b ON (b.hflog_id = a.id)
						LEFT JOIN " . \Ode\DBO\SWLocation::TABLE_NAME . " AS c ON (c.id = b.sw_loc_id)
						LEFT JOIN " . \Ode\DBO\SWStation::TABLE_NAME . " AS d ON (d.id = c.station_id)
						WHERE a.frequency = " . \Ode\DBO::getInstance()->quote($sQry, \PDO::PARAM_INT) . "
						OR a.description LIKE " . \Ode\DBO::getInstance()->quote($qry, \PDO::PARAM_STR) . "
						OR c.site LIKE " . \Ode\DBO::getInstance()->quote($qry, \PDO::PARAM_STR) . "
						OR c.frequency = " . \Ode\DBO::getInstance()->quote($sQry, \PDO::PARAM_INT) . "
						OR d.title LIKE " . \Ode\DBO::getInstance()->quote($qry, \PDO::PARAM_STR) . "
						ORDER BY a.time_on
						DESC";
                //print $sql;

                $logs = \Ode\DBO::getInstance()->query($sql)->fetchAll(\PDO::FETCH_CLASS, \Ode\DBO\Hflog::MODEL_NAME);

                $pager = \Pager::factory(array(
                    'mode' => "Sliding",
                    'perPage' => 10,
                    'delta' => 1,
                    'urlVar' => 'page',
                    'append' => false,
                    'path' => '',
                    'fileName' => "javascript:searchLogs('" + $sQry + "', %d);",
                    'itemData' => $logs
                ));

                \Ode\View::getInstance()->assign("logs", $pager->getPageData());
                \Ode\View::getInstance()->assign("pagelinks", $pager->getLinks());
                \Ode\View::getInstance()->assign("terms", $sQry);

                echo \Ode\View::getInstance()->fetch("logbook/searchList.tpl.php");
                exit();
                break;
        }
        break;
    case 'list':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:
                break;
            case 'all':
                $logs = \Ode\DBO::getInstance()->query("
					SELECT a.*
					FROM " . \Ode\DBO\Hflog::TABLE_NAME . " AS a
					ORDER BY a.time_on
					DESC
				")->fetchAll(PDO::FETCH_CLASS, \Ode\DBO\Hflog::MODEL_NAME);

                $pager = \Pager::factory(array(
                    'mode' => "Sliding",
                    'perPage' => 10,
                    'delta' => 1,
                    'urlVar' => 'page',
                    'append' => false,
                    'path' => '',
                    'fileName' => 'javascript:showAllLogs(%d);',
                    'itemData' => $logs
                ));

                \Ode\View::getInstance()->assign("logs", $pager->getPageData());
                \Ode\View::getInstance()->assign("pagelinks", $pager->getLinks());

                echo \Ode\View::getInstance()->fetch("logbook/logList.tpl.php");
                exit();
                break;
        }
        break;
    case 'get':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:
                break;
            case 'one':
                $log = \Ode\DBO\Hflog::getOneById($_POST['id']);

                \Ode\View::getInstance()->assign("log", $log);

                echo \Ode\View::getInstance()->fetch("logbook/infoWindow.tpl.php");
                exit();
                break;
        }
        break;
    case 'whatson':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:
                break;
            case 'now':
                $logs = \Ode\DBO::getInstance()->query("
					SELECT a.*
					FROM hflogs AS a
					WHERE (HOUR(time_on) = hour(UTC_TIMESTAMP())
					AND DAYOFWEEK(time_on) = DAYOFWEEK(UTC_TIMESTAMP()))
					OR (HOUR(time_off) = HOUR(UTC_TIMESTAMP())
					AND DAYOFWEEK(time_off) = DAYOFWEEK(UTC_TIMESTAMP()))"
                )->fetchAll(PDO::FETCH_CLASS, \Ode\DBO\Hflog::MODEL_NAME);

                $pager = \Pager::factory(array(
                    'mode' => "Sliding",
                    'perPage' => 10,
                    'delta' => 1,
                    'urlVar' => 'page',
                    'append' => false,
                    'path' => '',
                    'fileName' => 'javascript:thisHour(%d);',
                    'itemData' => $logs
                ));

                \Ode\View::getInstance()->assign("logs", $pager->getPageData());
                \Ode\View::getInstance()->assign("pagelinks", $pager->getLinks());

                echo \Ode\View::getInstance()->fetch("logbook/logList.tpl.php");
                exit();
                break;
            case 'any':
                $logs = \Ode\DBO::getInstance()->query("
					SELECT a.*
					FROM hflogs AS a
					WHERE HOUR(time_on) = HOUR(UTC_TIMESTAMP())
					OR HOUR(time_off) = HOUR(UTC_TIMESTAMP())"
                )->fetchAll(PDO::FETCH_CLASS, \Ode\DBO\Hflog::MODEL_NAME);

                $pager = \Pager::factory(array(
                    'mode' => "Sliding",
                    'perPage' => 10,
                    'delta' => 1,
                    'urlVar' => 'page',
                    'append' => false,
                    'path' => '',
                    'fileName' => 'javascript:thisHour(%d, true);',
                    'itemData' => $logs
                ));

                \Ode\View::getInstance()->assign("logs", $pager->getPageData());
                \Ode\View::getInstance()->assign("pagelinks", $pager->getLinks());

                echo \Ode\View::getInstance()->fetch("logbook/logList.tpl.php");
                exit();
                break;
        }
        break;
}
?>