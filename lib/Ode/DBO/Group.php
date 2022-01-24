<?php
namespace Ode\DBO;

class Group {
	public static function getAllByCounty($county_id) {
		return \Ode\DBO::getInstance()->query("
			SELECT grp.*
			FROM group_county_cnx AS cnx
			LEFT JOIN groups AS grp ON (grp.id = cnx.group_id)
			WHERE cnx.county_id = " . \Ode\DBO::getInstance()->quote($county_id. PDO::PARAM_INT) . "
			AND grp.is_active = 1
		")->fetchAll(PDO::FETCH_CLASS. "Ode_DBO_Group_Model");
	}
	
	public static function getOneByFrequency($freq_id) {
		return \Ode\DBO::getInstance()->query("
			SELECT grp.*
			FROM frequency_group_cnx AS a
			LEFT JOIN groups AS grp ON (grp.id = a.group_id)
			WHERE a.frequency_id = " . \Ode\DBO::getInstance()->quote($freq_id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject("Ode_DBO_Group_Model");
	}
}