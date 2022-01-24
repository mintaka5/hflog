<?php
namespace Ode\DBO;

use Ode\DBO;

class SWAudio {
	const TABLE_NAME = 'sw_audio';
	const COLUMNS = 'a.id, a.filename, a.title';
	const MODEL_NAME = 'Ode\DBO\SWAudio\Model';

    public static function getOneById($id) {
        return DBO::getInstance()->query("
            SELECT " . self::COLUMNS . "
            FROM " . self::TABLE_NAME . " AS a
            WHERE a.id = " . DBO::getInstance()->quote($id, \PDO::PARAM_INT) . "
            LIMIT 0,1
        ")->fetchObject(self::MODEL_NAME);
    }
}