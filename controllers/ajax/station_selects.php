<?php
require_once './init.php';

$stns = \Ode\DBO::getInstance()->query("
	SELECT a.*
	FROM " . \Ode\DBO\SWStation::TABLE_NAME . " AS a
	ORDER BY a.title
	ASC
")->fetchAll(\PDO::FETCH_ASSOC);

Util::json($stns);
exit();
