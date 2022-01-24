<?php
require_once './init.php';

switch(\Ode\Manager::getInstance()->getMode()) {
	default:
		$counties = \Ode\DBO::getInstance()->query("
			SELECT county.*
			FROM counties AS county
			WHERE county.state = " . \Ode\DBO::getInstance()->quote($_POST['s'], PDO::PARAM_STR) . "
			ORDER BY county.name
			ASC
		")->fetchAll(PDO::FETCH_OBJ);
		
		\Ode\View::getInstance()->assign("counties", $counties);
		
		echo \Ode\View::getInstance()->fetch("ajax/counties_select.tpl.php");
		break;
	case 'json':
		$json = new Services_JSON();
		
		$counties = \Ode\DBO::getInstance()->query("
			SELECT county.cid, county.name
			FROM counties AS county
			WHERE county.state = " . \Ode\DBO::getInstance()->quote($_POST['s'], PDO::PARAM_STR) . "
			AND county.is_active = 1
			ORDER BY county.name
			ASC
		")->fetchAll(PDO::FETCH_OBJ);
		
		header("Content-Type: application/json");
		echo $json->encode($counties);
		break;
	case 'session':
		$json = new Services_JSON();
		
		$_SESSION['_county'] = $_POST['c'];
		
		header("Content-Type: application/json");
		echo $json->encode($_POST['c']);
		break;
}
exit();
?>