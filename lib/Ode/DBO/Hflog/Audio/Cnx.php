<?php
namespace Ode\DBO\Hflog\Audio;

use Ode\DBO;

class Cnx {
	const TABLE_NAME = 'hflog_audio_cnx';
	const MODEL_NAME = 'Ode\DBO\Hflog\Audio\Cnx\Model';
	const COLUMNS = 'a.id, a.log_id, a.audio_id';

    public static function deleteAllByLog($logId) {
        $sth = DBO::getInstance()->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE log_id = :logid");
        $sth->bindParam(":logid", $logId, \PDO::PARAM_STR, 50);

        try {
            $sth->execute();
        } catch (\PDOException $e) {
            error_log($e->getMessage(), 0);
        } catch (\Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public static function delete($id) {
        $sth = DBO::getInstance()->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE id = :id");
        $sth->bindParam(":id", $id, \PDO::PARAM_INT, 11);

        try {
            $sth->execute();
        } catch (\PDOException $e) {
            error_log($e->getMessage(), 0);
        } catch (\Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }
}