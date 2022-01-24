<?php
namespace Ode\DBO\Frequency;

class Mode {
	public static function getOneById($id) {
		return \Ode\DBO::getInstance()->query("
			SELECT mode.*
			FROM frequency_modes AS mode
			WHERE id = " . \Ode\DBO::getInstance()->quote($id, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject("Ode_DBO_Frequency_Mode_Model");
	}
	
	public static function getIdByMode($mode) {
		return \Ode\DBO::getInstance()->query("
			SELECT a.id
			FROM frequency_modes AS a
			WHERE a.title = " . \Ode\DBO::getInstance()->quote($mode, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchColumn();
	}
}
?>
