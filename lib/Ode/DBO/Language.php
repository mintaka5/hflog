<?php
namespace Ode\DBO;

use Ode\DBO;

class Language
{
    const TABLE_NAME = 'languages';
    const MODEL_NAME = '\Ode\DBO\Language\Model';

    public static function getOneByISO($iso)
    {
        return DBO::getInstance()->query("
			SELECT a.*
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.iso = " . DBO::getInstance()->quote($iso, \PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
    }

    public static function getAll($order = "language", $sort = "ASC")
    {
        return DBO::getInstance()->query("
                SELECT a.*
                FROM " . self::TABLE_NAME . " AS a
                ORDER BY a." . $order . "
                " . $sort . "
            ")->fetchAll(\PDO::FETCH_CLASS, self::MODEL_NAME);
    }
}