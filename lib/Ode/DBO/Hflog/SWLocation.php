<?php
namespace Ode\DBO\Hflog;

use Ode\DBO;

class SWLocation
{
    const TABLE_NAME = 'hflog_sw_locations';
    const MODEL_NAME = '\Ode\DBO\Hflog\SWLocation\Model';
    const COLUMNS = 'a.id, a.sw_loc_id, a.hflog_id';

    /**
     * Delete relationship between location and log
     * @param string $hflog_id
     */
    public static function delete($hflog_id)
    {
        $sth = DBO::getInstance()->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE hflog_id = :id");
        $sth->bindParam(":id", $hflog_id, \PDO::PARAM_STR, 50);

        try {
            $sth->execute();
        } catch (\Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public static function deleteAllByLocation($locationId) {
        $sth = DBO::getInstance()->prepare("
            DELETE FROM " . self::TABLE_NAME . "
            WHERE sw_loc_id = :swlocid
        ");
        $sth->bindParam(":swlocid", $locationId, \PDO::PARAM_INT, 11);

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

    public static function getOneByHflog($hflog_id)
    {
        return DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE hflog_id = " . DBO::getInstance()->quote($hflog_id, \PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
    }

    public static function connect($location_id, $log_id)
    {
        $cnx = self::getOneByHflog($log_id);

        if ($cnx != false) { // update
            self::update($cnx->id, $location_id);
        } else {
            self::insert($location_id, $log_id);
        }
    }

    public static function update($cnx_id, $location_id)
    {
        $sth = DBO::getInstance()->prepare("
			UPDATE " . self::TABLE_NAME . "
			SET sw_loc_id = :loc_id
			WHERE id = :cnx_id
		");
        $sth->bindParam(":loc_id", $location_id, \PDO::PARAM_INT, 11);
        $sth->bindParam(":cnx_id", $cnx_id, \PDO::PARAM_INT, 11);

        try {
            $sth->execute();
        } catch (\Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public static function insert($location_id, $log_id)
    {
        $sth = DBO::getInstance()->prepare("
				INSERT INTO " . self::TABLE_NAME . " (sw_loc_id, hflog_id)
				VALUES (:loc, :log)
				");
        $sth->bindParam(":loc", $location_id, \PDO::PARAM_INT, 11);
        $sth->bindParam(":log", $log_id, \PDO::PARAM_STR, 50);

        try {
            $sth->execute();
        } catch (\Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }
}