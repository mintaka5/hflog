<?php
namespace Ode\DBO;

use Ode\DBO;
use Ode\DBO\SWLocation\Entity as SWLocationEntity;
use Ode\DBO\SWLocation\Entity;

class SWLocation
{
    const TABLE_NAME = 'sw_locations';
    const MODEL_NAME = '\Ode\DBO\SWLocation\Model';
    const COLUMNS = 'a.id,a.station_id,a.lat,a.lng,a.azimuth,a.site,a.start_utc,a.end_utc,a.days,a.frequency,a.power,a.lang_iso,a.is_active,a.modified';

    public static function getOneById($id)
    {
        return DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.id = " . DBO::getInstance()->quote($id, \PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
    }

    public static function getAllByStation($station_id, $order = "site", $sort = "ASC")
    {
        return DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.station_id = " . DBO::getInstance()->quote($station_id, \PDO::PARAM_STR) . "
			ORDER BY a." . $order . "
			" . $sort . "
		")->fetchAll(\PDO::FETCH_CLASS, self::MODEL_NAME);
    }

    public static function setLanguage($loc_id, $lang_iso)
    {
        $sth = DBO::getInstance()->prepare("
                UPDATE " . self::TABLE_NAME . "
                SET
                    lang_iso = :lang_iso
                WHERE id = :id
            ");
        $sth->bindParam(":lang_iso", $lang_iso, \PDO::PARAM_STR, 4);
        $sth->bindParam(":id", $loc_id, \PDO::PARAM_INT, 11);

        try {
            $sth->execute();

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage(), 0);

            return false;
        }
    }

    /**
     * @param SWLocationEntity $location
     */
    public static function insertOne(SWLocationEntity $location) {
        DBO::getInstance()->beginTransaction();

        $sth = DBO::getInstance()->prepare("
            INSERT INTO " . self::TABLE_NAME . " (station_id, lat, lng, site, start_utc, end_utc, frequency, lang_iso, is_active, modified)
            VALUES (:station, :lat, :lng, :site, :start, :endutc, :freq, :langiso, 0, NOW())
        ");
        $sth->bindParam(":station", $location->station_id, \PDO::PARAM_STR, 50);
        $sth->bindParam(":lat", $location->lat, \PDO::PARAM_INT);
        $sth->bindParam(":lng", $location->lng, \PDO::PARAM_INT);
        $sth->bindParam(":site", $location->site, \PDO::PARAM_STR);
        $sth->bindParam(":start", $location->start_utc, \PDO::PARAM_STR);
        $sth->bindParam(":endutc", $location->end_utc, \PDO::PARAM_STR);
        $sth->bindParam(":freq", $location->frequency, \PDO::PARAM_INT);
        $sth->bindParam(":langiso", $location->lang_iso, \PDO::PARAM_STR, 4);

        try {
            $sth->execute();
        } catch (\PDOException $e) {
            error_log($e->getTraceAsString(), 0);
            return false;
        } catch(\Exception $e) {
            error_log($e->getTraceAsString(), 0);
            return false;
        }

        $newId = DBO::getInstance()->query('SELECT LAST_INSERT_ID()')->fetchColumn();

        DBO::getInstance()->commit();

        return $newId;
    }

    public static function updateOne(SWLocationEntity $location) {
        $sth = DBO::getInstance()->prepare("
            UPDATE " . self::TABLE_NAME . "
            SET
                station_id = :station,
                lat = :lat,
                lng = :lng,
                site = :site,
                start_utc = :start,
                end_utc = :endutc,
                frequency = :freq,
                lang_iso = :langiso,
                is_active = 0,
                modified = NOW()
            WHERE id = :id
        ");
        $sth->bindParam(":station", $location->station_id, \PDO::PARAM_STR, 50);
        $sth->bindParam(":lat", $location->lat, \PDO::PARAM_INT);
        $sth->bindParam(":lng", $location->lng, \PDO::PARAM_INT);
        $sth->bindParam(":site", $location->site, \PDO::PARAM_STR);
        $sth->bindParam(":start", $location->start_utc, \PDO::PARAM_STR);
        $sth->bindParam(":endutc", $location->end_utc, \PDO::PARAM_STR);
        $sth->bindParam(":freq", $location->frequency, \PDO::PARAM_INT);
        $sth->bindParam(":langiso", $location->lang_iso, \PDO::PARAM_STR, 4);
        $sth->bindParam(":id", $location->id, \PDO::PARAM_INT, 11);

        try {
            $sth->execute();
        } catch (\PDOException $e) {
            error_log($e->getTraceAsString(), 0);
            return false;
        } catch(\Exception $e) {
            error_log($e->getTraceAsString(), 0);
            return false;
        }

        return true;
    }

    public static function getAll($order = 'site', $sort = 'ASC') {
        return DBO::getInstance()->query('
            SELECT ' . self::COLUMNS . '
            FROM ' . self::TABLE_NAME . ' AS a
            ORDER BY a.' . $order . '
            ' . $sort . '
        ')->fetchAll(\PDO::FETCH_CLASS, self::MODEL_NAME);
    }

    public static function getInactive($order = 'site', $sort = 'ASC') {
        return DBO::getInstance()->query('
            SELECT ' . self::COLUMNS . '
            FROM ' . self::TABLE_NAME . ' AS a
            WHERE a.is_active = ' . DBO::getInstance()->quote('0', \PDO::PARAM_INT) . '
            ORDER BY a.' . $order . '
            ' . $sort . '
        ')->fetchAll(\PDO::FETCH_CLASS, self::MODEL_NAME);
    }

    /**
     * @param int $id
     * @param int $isActive
     * @return bool
     */
    public static function setActive($id, $isActive = 1) {
        $sth = DBO::getInstance()->prepare("
            UPDATE " . self::TABLE_NAME . "
            SET
                is_active = :is_active
            WHERE id = :id
        ");
        $sth->bindParam(":is_active", $isActive, \PDO::PARAM_INT, 1);
        $sth->bindParam(":id", $id, \PDO::PARAM_INT, 11);

        try {
            $sth->execute();
        } catch (\PDOException $e) {
            error_log($e->getTraceAsString());
            return false;
        } catch(\Exception $e) {
            error_log($e->getTraceAsString());
            return false;
        }

        return true;
    }

    /**
     * @param string $stationId
     * @return bool
     */
    public static function deleteAllByStation($stationId) {
        $sth = DBO::getInstance()->prepare("
            DELETE FROM " . self::TABLE_NAME . "
            WHERE station_id = :stationid
        ");
        $sth->bindParam(":stationid", $stationId, \PDO::PARAM_STR, 50);

        try {
            $sth->execute();

            // delete all log connections
            $locations = self::getAllByStation($stationId);
            foreach ($locations as $location) {
                DBO\Hflog\SWLocation::deleteAllByLocation($location->id);
            }
        } catch (\PDOException $e) {
            error_log($e->getTraceAsString());
            return false;
        } catch(\Exception $e) {
            error_log($e->getTraceAsString());
            return false;
        }

        return true;
    }

    /**
     * @param integer $id location id
     * @return bool
     */
    public static function delete($id) {
        $sth = DBO::getInstance()->prepare("
            DELETE FROM " . self::TABLE_NAME . "
            WHERE id = :id
        ");
        $sth->bindParam(":id", $id, \PDO::PARAM_INT, 11);

        try {
            $sth->execute();

            // delete all log connections
            DBO\Hflog\SWLocation::deleteAllByLocation($id);
        } catch (\PDOException $e) {
            error_log($e->getTraceAsString());
            return false;
        } catch(\Exception $e) {
            error_log($e->getTraceAsString());
            return false;
        }

        return true;
    }
}