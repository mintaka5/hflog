<?php
require_once './init.php';

$grpId = UUID::get();

$sth = \Ode\DBO::getInstance()->prepare("
	INSERT INTO groups (`id`, `title`, `description`, `is_active`)
	VALUES (:id, :title, :desc, 1)
");
$sth->bindValue(":id", $grpId, PDO::PARAM_STR);
$sth->bindValue(":title", trim($_POST['title']), PDO::PARAM_STR);
$sth->bindValue(":desc", trim($_POST['desc']), PDO::PARAM_STR);

$result = array();
try {
	$sth->execute();

	$sth = \Ode\DBO::getInstance()->prepare("
		INSERT INTO group_county_cnx (group_id, county_id)
		VALUES (:group_id, :county_id)
	");
	$sth->bindValue(":group_id", $grpId, PDO::PARAM_STR);
	$sth->bindValue(":county_id", $_POST['cid'], PDO::PARAM_INT);
	
	$result['data']['group_id'] = $grpId;
	$result['data']['county_id'] = $_POST['cid'];
	$result['data']['group_title'] = trim($_POST['title']);
	
	try {
		$sth->execute();
		
		$result['data']['cnx_id'] = \Ode\DBO::getInstance()->query("SELECT LAST_INSERT_ID()")->fetchColumn();
	} catch(PDOException $e) {
		Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
		
		$result['status'] = false;
	}
	
	$result['status'] = true;
} catch(PDOException $e) {
	Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
	
	$result['status'] = false;
}
$json = new Services_JSON();
header("Content-Type: application/json");
echo $json->encode($result);
exit();
?>