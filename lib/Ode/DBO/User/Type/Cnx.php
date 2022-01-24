<?php
namespace Ode\DBO\User\Type;

use Ode\DBO;

class Cnx {
    const TABLE = 'user_type_cnx';
    const COLUMNS = 'a.id,a.user_id,a.type_id';
    const MODEL = '\Ode\DBO\User\Type\Cnx\Model';
    
	public static function getOneByUser($id) {
		return DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE . " AS a
			WHERE a.user_id = " . DBO::getInstance()->quote($id, \PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL);
	}
}