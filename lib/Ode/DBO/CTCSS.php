<?php
namespace Ode\DBO;

class CTCSS {
	public static function getIdByHertz($hertz) {
		return \Ode\DBO::getInstance()->query("
			SELECT ctcss.id
			FROM ctcss_tones AS ctcss
			WHERE ctcss.hertz = " . \Ode\DBO::getInstance()->quote($hertz, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchColumn();
	}
	
	public static function getOneById($id) {
		return \Ode\DBO::getInstance()->query("
			SELECT ctcss.*
			FROM ctcss_tones AS ctcss
			WHERE ctcss.id = " . \Ode\DBO::getINstance()->quote($id, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject("Ode_DBO_CTCSS_Model");
	}
}
?>