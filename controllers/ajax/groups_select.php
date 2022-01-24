<?php
require_once './init.php';

switch(\Ode\Manager::getInstance()->getMode()) {
	default:
		
		break;
	case 'json':
		$json = new Services_JSON();
		
		$groups = \Ode\DBO::getInstance()->query("
			SELECT `group`.id, `group`.title
			FROM group_county_cnx AS cnx
			LEFT JOIN groups AS `group` ON (`group`.id = cnx.group_id)
			WHERE cnx.county_id = " . \Ode\DBO::getInstance()->quote($_POST['c'], PDO::PARAM_INT) . "
			AND group.is_active = 1
			ORDER BY `group`.title
		")->fetchAll(PDO::FETCH_OBJ);
		
		header("Content-Type: application/json");
		echo $json->encode($groups);
		break;
}

exit();
?>