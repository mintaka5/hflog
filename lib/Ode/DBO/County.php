<?php
namespace Ode\DBO;

class County {
	public static function getOneByGroup($group_id) {
		try {
			$results = \Ode\DBO::getInstance()->query("
				SELECT county.*
				FROM group_county_cnx AS cnx
				LEFT JOIN counties AS county ON (county.cid = cnx.county_id)
				WHERE cnx.group_id = " . \Ode\DBO::getInstance()->quote($group_id, PDO::PARAM_STR) . "
				LIMIT 0,1
			")->fetchObject("Ode_DBO_County_Model");
			
			return $results;
		} catch(PDOException $e) {
			Ode_Log::getInstance()->log($e->getTraceAsString(), E_ERROR);
		}
		
		return false;
	}
	
	public static function getOneById($id) {
		return \Ode\DBO::getInstance()->query("
			SELECT cty.*
			FROM counties AS cty
			WHERE cty.cid = " . \Ode\DBO::getInstance()->quote($id, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject("Ode_DBO_County_Model");
	}
}