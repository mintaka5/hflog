<?php
require_once './init.php';

$groups = \Ode\DBO::getInstance()->query("
	SELECT `group`.*
	FROM group_county_cnx AS cnx
	LEFT JOIN groups AS `group` ON (`group`.id = cnx.group_id)
	WHERE cnx.county_id = " . \Ode\DBO::getInstance()->quote($_POST['c'], PDO::PARAM_INT) . "
	ORDER BY `group`.title
	ASC
")->fetchAll(PDO::FETCH_CLASS, "Ode_DBO_Group_Model");

$county = Ode_DBO_County::getOneById($_POST['c']);

\Ode\View::getInstance()->assign("groups", $groups);
\Ode\View::getInstance()->assign("county", $county);

header("Content-Type: text/html");
echo \Ode\View::getInstance()->fetch("ajax/frequencies_list.tpl.php");
exit();
?>