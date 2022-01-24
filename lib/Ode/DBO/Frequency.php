<?php
namespace Ode\DBO;

class Frequency {
	public static function getAllByGroup($group_id) {
		try {
			$results = \Ode\DBO::getInstance()->query("
				SELECT freq.*
				FROM frequency_group_cnx AS cnx
				LEFT JOIN frequencies AS freq ON (freq.id = cnx.frequency_id)
				WHERE cnx.group_id = " . \Ode\DBO::getInstance()->quote($group_id, PDO::PARAM_STR) . "
				AND freq.is_active = 1
				ORDER BY freq.frequency
				ASC
			")->fetchAll(PDO::FETCH_CLASS, "Ode_DBO_Frequency_Model");
			
			return $results;
		} catch(PDOException $e) {
			Ode_Log::getInstance()->log($e->getTraceAsString(), E_ERROR);
		}
		
		return false;
	}
	
	public static function addFrequency($county_id, $freq, $tag, $ctcss, $dcs, $nac, $enc, $mode, $desc, $user_id) {
		$freqId = UUID::get();
		
		$sth = \Ode\DBO::getInstance()->prepare("INSERT INTO frequencies (
			`id`, `frequency`, `tag`, `ctcss_tone_id`, `dcs_tone_id`, `nac`, `is_encrypted`, `mode_id`,
			`description`, `is_active`, `user_id`, `created`, `modified`
			) VALUES (:id, :freq, :tag, :ctcss, :dcs, :nac, :enc, :mode, :desc, 0, :user_id, NOW(), NOW())");
		$sth->bindValue(":id", $freqId, PDO::PARAM_STR);
		$sth->bindValue(":freq", trim($freq), PDO::PARAM_INT);
		$sth->bindValue(":tag", trim($tag), PDO::PARAM_STR);
		$sth->bindValue(":ctcss", empty($ctcss) ? null : $ctcss, empty($ctcss) ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$sth->bindValue(":dcs", empty($dcs) ? null : $dcs, empty($dcs) ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$sth->bindValue(":nac", trim($nac), PDO::PARAM_STR);
		$sth->bindValue(":enc", $enc, PDO::PARAM_INT);
		$sth->bindValue(":mode", $mode, PDO::PARAM_INT);
		$sth->bindValue(":desc", trim($desc), PDO::PARAM_STR);
		$sth->bindValue(":user_id", $user_id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
			
			Ode_DBO_Frequency_County_Cnx::addFrequencyToCounty($freqId, $county_id);
		} catch(PDOException $e) {
			Ode_Log::getInstance()->log($e->getTraceAsString(), E_ERROR);
		}
	}
	
	public static function getOneById($id) {
		return \Ode\DBO::getInstance()->query("
			SELECT freq.*
			FROM frequencies AS freq
			WHERE freq.id = " . \Ode\DBO::getInstance()->quote($id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject("Ode_DBO_Frequency_Model");
	}
	
	public function getAllByCounty($county_id) {
		return \Ode\DBO::getInstance()->query("
			SELECT freq.*
			FROM frequency_county_cnx AS cnx
			LEFT JOIN frequencies AS freq ON (freq.id = cnx.frequency_id)
			WHERE cnx.county_id = " . \Ode\DBO::getInstance()->quote($county_id, PDO::PARAM_INT) . "
			AND freq.is_active = 1
		")->fetchAll(PDO::FETCH_CLASS, "Ode_DBO_Frequency_Model");
	}
}