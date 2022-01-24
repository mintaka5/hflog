<?php
require_once './init.php';

$searchStr = "%" . preg_replace("/[\s\t\r\n\W]/", "%", trim($_POST['q'])) . "%";

$stns = \Ode\DBO::getInstance()->query("
	SELECT " . \Ode\DBO\SWStation::COLUMNS . "
	FROM " . \Ode\DBO\SWStation::TABLE_NAME . " AS a
	WHERE a.title LIKE " . \Ode\DBO::getInstance()->quote($searchStr, \PDO::PARAM_STR) . "
	ORDER BY a.title
	ASC
")->fetchAll(\PDO::FETCH_ASSOC);

Util::json($stns);
exit();
