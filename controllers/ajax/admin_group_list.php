<?php
require_once './init.php';

$groups = \Ode\DBO::getInstance()->query("
	SELECT `group`.*
	FROM group_county_cnx AS cnx
	LEFT JOIN groups AS `group` ON (`group`.id = cnx.group_id)
	WHERE cnx.county_id = " . \Ode\DBO::getInstance()->quote($_POST['cid'], PDO::PARAM_INT) . "
	AND group.is_active = 1
	ORDER BY `group`.title
	ASC 
")->fetchAll(PDO::FETCH_CLASS, "Ode_DBO_Group_Model");
					
\Ode\View::getInstance()->assign("freq_id", $_POST['fid']);
\Ode\View::getInstance()->assign("groups", $groups);
\Ode\View::getInstance()->assign("county", $groups[0]->county());
header("Content-Type: text/html");
echo \Ode\View::getInstance()->fetch("ajax/admin_group_list.tpl.php");
exit();
?>
