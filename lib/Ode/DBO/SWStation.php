<?php
namespace Ode\DBO;

use Ode\DBO;
use Ode\Geo\Util;

class SWStation
{
    const TABLE_NAME = 'sw_stations';
    const MODEL_NAME = '\Ode\DBO\SWStation\Model';
    const COLUMNS = 'a.id,a.station_name,a.title,a.is_active,a.modified';

    public static function getOneById($id)
    {
        return DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.id = " . DBO::getInstance()->quote($id, \PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
    }

    public static function getOneByName($name)
    {
        return DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.station_name = " . DBO::getInstance()->quote($name, \PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
    }

    public static function getAll($order = "title", $sort = "ASC")
    {
        return DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			ORDER BY a." . $order . "
			" . $sort . "
		")->fetchAll(\PDO::FETCH_CLASS, self::MODEL_NAME);
    }

    public static function insertOne($title, $name = false)
    {
        $uuid = DBO::getInstance()->query('SELECT UUID()')->fetchColumn();

        $newName = ($name === false) ? self::generateName($title) : $name;

        $sth = DBO::getInstance()->prepare("
			INSERT INTO " . self::TABLE_NAME . " (id, station_name, title, is_active, modified)
			VALUES (:id, :sname, :title, 0, NOW())
		");
        $sth->bindParam(":id", $uuid, \PDO::PARAM_STR, 50);
        $sth->bindParam(":sname", $newName, \PDO::PARAM_STR, 45);
        $sth->bindParam(":title", $title, \PDO::PARAM_STR, 255);

        try {
            $sth->execute();
        } catch (\PDOException $e) {
            error_log($e->getTraceAsString(), 0);
        } catch (\Exception $e) {
            error_log($e->getTraceAsString(), 0);
        }

        return $uuid;
    }

    public static function nameExists($name) {
        $id = DBO::getInstance()->query('
            SELECT a.id
            FROM ' . self::TABLE_NAME . ' AS a
            WHERE a.station_name = ' . DBO::getInstance()->quote($name, \PDO::PARAM_STR) . '
            LIMIT 0,1
        ')->fetchColumn();

        if(empty($id)) {
            return false;
        }

        return true;
    }

    public static function generateName($name) {
        $newName = \Util::camelCase($name) . \Util::randomString(5);

        if(self::nameExists($newName)) {
            self::generateName($name);
        }

        return $newName;
    }

    public static function setActive($id, $isActive = 1) {
        $sth = DBO::getInstance()->prepare("
            UPDATE " . self::TABLE_NAME . "
            SET
                is_active = :is_active
            WHERE id = :id
        ");
        $sth->bindParam(":is_active", intval($isActive), \PDO::PARAM_INT, 1);
        $sth->bindParam(":id", $id, \PDO::PARAM_STR, 50);

        try {
            $sth->execute();
        } catch (\PDOException $e) {
            error_log($e->getTraceAsString(), 0);
        } catch (\Exception $e) {
            error_log($e->getTraceAsString(), 0);
        }

        return $id;
    }

    public static function getInactive($order = 'title', $sort = 'ASC') {
        return DBO::getInstance()->query('
            SELECT ' . self::COLUMNS . '
            FROM ' . self::TABLE_NAME . ' AS a
            WHERE a.is_active = ' . DBO::getInstance()->quote('0', \PDO::PARAM_INT) . '
            ORDER BY a.' . $order . '
            ' . $sort . '
        ')->fetchAll(\PDO::FETCH_CLASS, self::MODEL_NAME);
    }

    public static function delete($id, $locations = true) {
        $sth = DBO::getInstance()->prepare("
            DELETE FROM " . self::TABLE_NAME . "
            WHERE id = :id
        ");
        $sth->bindParam(":id", $id, \PDO::PARAM_STR, 50);

        try {
            $sth->execute();

            if($locations === true) {
                SWLocation::deleteAllByStation($id);
            }
        } catch(\PDOException $e) {
            error_log($e->getTraceAsString(), 0);
        } catch(\Exception $e) {
            error_log($e->getTraceAsString(), 0);
        }
    }

    public static function getIdByName($stationName) {
        return DBO::getInstance()->query('
            SELECT a.id
            FROM ' . self::TABLE_NAME . ' AS a
            WHERE a.station_name = ' . DBO::getInstance()->quote($stationName, \PDO::PARAM_STR) . '
            LIMIT 0,1
        ')->fetchColumn();
    }

    public static function update(DBO\SWStation\Entity $entity) {
        $sth = DBO::getInstance()->prepare("
            UPDATE " . self::TABLE_NAME . "
            SET
                station_name = :stnname,
                title = :title,
                is_active = :isactive,
                modified = :modified
            WHERE id = :id
        ");
        $sth->bindParam(":stnname", $entity->station_name, \PDO::PARAM_STR, 45);
        $sth->bindParam(":title", $entity->title, \PDO::PARAM_STR, 255);
        $sth->bindParam(":isactive", $entity->is_active, \PDO::PARAM_INT, 1);
        $sth->bindParam(":modified", $entity->modified, \PDO::PARAM_STR);
        $sth->bindParam(":id", $entity->id, \PDO::PARAM_STR, 50);

        try {
            $sth->execute();
        } catch (\PDOException $e) {
            error_log($e->getTraceAsString(), 0);
            return false;
        } catch (\Exception $e) {
            error_log($e->getTraceAsString(), 0);
            return false;
        }

        return $entity;
    }
}