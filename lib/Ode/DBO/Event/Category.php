<?php
namespace Ode\DBO\Event;

class Category {
	public static function getOneByID($id) {
		$sth = \Ode\DBO::getInstance()->prepare("
			SELECT a.*
			FROM event_categories AS a
			WHERE a.id = :id
		");
		$sth->bindValue(":id", $id, PDO::PARAM_INT);
		
		try {
			$sth->execute();
			
			return $sth->fetchObject("Ode_DBO_Event_Category_Model");
		} catch(PDOException $e) {
			Ode_Log::getInstance()->log($e->getMessage(), PEAR_LOG_WARNING);
			return false;
		}
	}
	
	public static function getAllByEventID($id) {
		$sth = \Ode\DBO::getInstance()->prepare("
			SELECT cat.*
			FROM event_category_cnx AS cnx
			LEFT JOIN event_categories AS cat ON (cat.id = cnx.category_id)
			WHERE cnx.event_id = :event_id
			ORDER BY cat.title
			ASC
		");
		$sth->bindValue(":event_id", $id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
			
			return $sth->fetchAll(PDO::FETCH_CLASS, "Ode_DBO_Event_Category_Model");
		} catch(PDOException $e) {
			Ode_Log::getInstance()->log($e->getMessage(), PEAR_LOG_WARNING);
			return false;
		}
	}
}