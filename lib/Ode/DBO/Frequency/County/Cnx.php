<?php
namespace Ode\DBO\Frequency\County;

class Cnx {
	const TABLE_NAME = "frequency_county_cnx";
	
	public static function addFrequencyToCounty($freq_id, $county_id) {
		if(!self::relationExists($freq_id, $county_id)) {
			$sth = \Ode\DBO::getInstance()->prepare("
				INSERT INTO " . self::TABLE_NAME . " (
					`frequency_id`, `county_id`
				) VALUES (
					:freq_id, :county_id
				)
			");
			$sth->bindValue(":freq_id", $freq_id, PDO::PARAM_STR);
			$sth->bindValue(":county_id", $county_id, PDO::PARAM_INT);
			
			try {
				$sth->execute();
				
				return \Ode\DBO::getInstance()->query("
					SELECT LAST_INSERT_ID() AS `id`
				")->fetchColumn();
			} catch(PDOException $e) {
				Ode_Log::getInstance()->log($e->getTraceAsString(), E_ERROR);
			}
		}
		
		return false;
	}
	
	private static function relationExists($freq_id, $county_id) {
		$result = \Ode\DBO::getInstance()->query("
			SELECT cnx.id 
			FROM " . self::TABLE_NAME . " AS cnx
			WHERE cnx.frequency_id = " . \Ode\DBO::getInstance()->quote($freq_id, PDO::PARAM_STR) . "
			AND cnx.county_id = " . \Ode\DBO::getInstance()->quote($county_id, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchColumn();
		
		if($result != false) {
			return true;
		}
		
		return false;
	}
}
?>