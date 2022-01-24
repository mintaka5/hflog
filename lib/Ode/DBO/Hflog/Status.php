<?php
namespace Ode\DBO\Hflog;

use Ode\DBO;

class Status {
    const TABLE_NAME = 'hflog_status';
    const MODEL_NAME = '\Ode\DBO\Hflog\Status\Model';
    const COLUMNS = 'a.id,a.hflog_id,a.status';

    const STATUS_INACTIVE = 'inactive';
    const STATUS_ACTIVE = 'active';
    const STATUS_NOT_APPROVED = 'not-approved';
    const STATUS_APPROVED = 'approved';
    
    public static function setStatus($id, $status) {
        $hasStatus = self::hasStatus($id);

        if(!$hasStatus) {
            $sth = DBO::getInstance()->prepare("
                    INSERT INTO " . self::TABLE_NAME . " (hflog_id, status) VALUES (
                        :logid, :status
                    ) 
                ");
            $sth->bindValue(":logid", $id, \PDO::PARAM_STR);
            $sth->bindValue(":status", $status, \PDO::PARAM_STR);

            try {
                $sth->execute();
            } catch (\PDOException $e) {
                error_log($e->getTraceAsString(), 0);
            }
        } else {
            $sth = DBO::getInstance()->prepare("
                UPDATE " . self::TABLE_NAME . "
                SET
                    status = :status
                WHERE hflog_id = :logid
            ");

            $sth->bindValue(":status", $status, \PDO::PARAM_STR);
            $sth->bindValue(":logid", $id, \PDO::PARAM_STR);

            try {
                $sth->execute();
            } catch (\PDOException $e) {
                error_log($e->getTraceAsString(), 0);
            }
        }
    }

    public static function hasStatus($id) {
        $status = DBO::getInstance()->query('
            SELECT a.id
            FROM ' . self::TABLE_NAME . ' AS a
            WHERE a.hflog_id = ' . DBO::getInstance()->quote($id, \PDO::PARAM_STR) . '
            LIMIT 0,1
        ')->fetchColumn();

        if(!empty($status)) {
            return true;
        }

        return false;
    }

    public static function getOneByLog($id) {
        return DBO::getInstance()->query('
            SELECT ' . self::COLUMNS . '
            FROM ' . self::TABLE_NAME . ' AS a
            WHERE a.hflog_id = ' . DBO::getInstance()->quote($id, \PDO::PARAM_STR) . '
            LIMIT 0,1
        ')->fetchObject(self::MODEL_NAME);
    }

    /**
     * Remove active logs from status table
     * @param $id Log ID
     */
    public static function delete($id) {
        $sth = DBO::getInstance()->prepare("
            DELETE FROM " . self::TABLE_NAME . "
            WHERE hflog_id = :id
        ");

        $sth->bindParam(":id", $id, \PDO::PARAM_STR, 50);

        try {
            $sth->execute();
        } catch (\PDOException $e) {
            error_log($e->getTraceAsString(), 0);
        }
    }
}