<?php
namespace Ode\DBO\SWStation;

use Ode\DBO;

class Audio {
	const TABLE_NAME = 'sw_station_audio';
	const MODEL_NAME = 'Ode\DBO\SWStation\Audio\Model';
	
	public static function getAllByStation($id) {
		return DBO::getInstance()->query("
			SELECT a.*
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.station_id = " . DBO::getInstance()->quote($id, \PDO::PARAM_STR) . "
		")->fetchAll(\PDO::FETCH_CLASS, self::MODEL_NAME);
	}
}
