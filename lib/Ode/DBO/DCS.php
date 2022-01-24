<?php
namespace Ode\DBO;

class DCS {
	public static function getOneById($id) {
		return \Ode\DBO::getInstance()->query("
			SELECT dcs.*
			FROM dcs_tones AS dcs
			WHERE dcs.id = " . \Ode\DBO::getInstance()->quote($id, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject();
	}
	
	public static function getIdByCode($code) {
		return \Ode\DBO::getInstance()->query("
			SELECT a.id
			FROM dcs_tones AS a
			WHERE a.dcs = " . \Ode\DBO::getInstance()->quote($code, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchColumn();
	}
}