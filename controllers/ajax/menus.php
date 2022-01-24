<?php
require_once './init.php';

/**
 * recent blog entries
 */
$blogs = \Ode\DBO::getInstance()->query("
	SELECT a.*
	FROM db125612_blog.wp_posts AS a
	WHERE a.post_type = 'post'
	AND a.post_status = 'publish'
	ORDER BY a.post_date
	DESC
	LIMIT 0,5
")->fetchAll(PDO::FETCH_CLASS, "Ode_DBO_Post_Model");
\Ode\View::getInstance()->assign("blogs", $blogs);
//Util::debug($blogs);

$recentLog = \Ode\DBO::getInstance()->query("
	SELECT a.*
	FROM " . \Ode\DBO\Hflog::TABLE_NAME . " AS a
	ORDER BY a.time_on
	DESC
	LIMIT 0,1
")->fetchObject(\Ode\DBO\Hflog::MODEL_NAME);

\Ode\View::getInstance()->assign("recentlog", $recentLog);

$jsonAry = array();

$jsonAry['menus'] = array(
    array('title' => "apps", 'content' => \Ode\View::getInstance()->fetch("menus/app.tpl.php")),
    array('title' => "blog", 'content' => \Ode\View::getInstance()->fetch("menus/blog.tpl.php"))
);

$json = new Services_JSON();
echo $json->encode($jsonAry);
exit();
?>
