<?php
namespace Ode\DBO\User;

use Ode\DBO;

class IRC {
    const TABLE_NAME = 'user_irc';
    const MODEL_NAME = 'Ode\DBO\User\IRC\Model';
    
    public static function getOneByUser($user_id) {
        return DBO::getInstance()->query("
            SELECT a.*
            FROM " . self::TABLE_NAME . " AS a
            WHERE a.user_id = " . DBO::getInstance()->quote($user_id, \PDO::PARAM_STR) . "
            LIMIT 0,1
        ")->fetchObject(self::MODEL_NAME);
    }
    
    public static function isRegistered($username, $hostmask) {
        return DBO::getInstance()->query("
            SELECT a.*
            FROM " . DBO\User::TABLE_NAME . " AS a
            LEFT JOIN " . self::TABLE_NAME . " AS b ON (b.user_id = a.id)
            WHERE b.hostmask = " . DBO::getInstance()->quote($hostmask, \PDO::PARAM_STR) . "
            AND a.username = " . DBO::getInstance()->quote($username, \PDO::PARAM_STR) . "
            AND b.is_deleted = 0
            LIMIT 0,1
        ")->fetchObject(DBO\User::MODEL_NAME);
    }
}
?>
