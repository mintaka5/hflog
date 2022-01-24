<?php
namespace Ode\DBO\Event;

class Location {
	public static function getOneByID($id) {
		try {
			$results = \Ode\DBO::getInstance()->query("
				SELECT loc.*
				FROM event_locations AS loc
				WHERE loc.id = " . \Ode\DBO::getInstance()->quote($id, PDO::PARAM_INT) . "
			")->fetchObject("Ode_DBO_Event_Location_Model");
			
			return $results;
		} catch(PDOException $e) {
			Ode_Log::getInstance()->log($e->getMessage(), PEAR_LOG_WARNING);
			
			return false;
		}
	}
	
	public static function getOneByEventID($id) {
		try {
			$results = \Ode\DBO::getInstance()->query("
				SELECT loc.*
				FROM event_locations AS loc
				LEFT JOIN event_location_cnx AS cnx ON (cnx.location_id = loc.id)
				WHERE cnx.event_id = " . \Ode\DBO::getInstance()->quote($id, PDO::PARAM_STR) . "
				LIMIT 0,1
			")->fetchObject("Ode_DBO_Event_Location_Model");
			
			return $results;
		} catch(PDOException $e) {
			Ode_Log::getInstance()->log($e->getMessage(), PEAR_LOG_WARNING);
			
			return false;
		}
	}
}