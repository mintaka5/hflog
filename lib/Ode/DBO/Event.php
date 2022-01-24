<?php
namespace Ode\DBO;

class Event {
	public static function getOneByID($id) {
		$sql = "SELECT a.*
				FROM events AS a
				WHERE a.id = :id";
		$sth = \Ode\DBO::getInstance()->prepare($sql);
		$sth->bindValue(":id", $id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
			return $sth->fetchObject("Ode_DBO_Event_Model");
		} catch(PDOException $e) {
			Ode_Log::getInstance()->log($e->getMessage(), PEAR_LOG_WARNING);
			
			return false;
		}
	}
}
?>