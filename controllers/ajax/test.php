<?php
require_once './init.php';

$json = new Services_JSON();

$users = \Ode\DBO::getInstance()->query("
	SELECT a.*
	FROM users AS a
")->fetchAll(PDO::FETCH_OBJ);

Util::json($users);
exit();
?>