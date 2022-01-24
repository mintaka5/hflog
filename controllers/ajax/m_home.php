<?php
require_once './init.php';

switch(\Ode\Manager::getInstance()->getMode()) {
	default: break;
	case 'blog':
		switch(\Ode\Manager::getInstance()->getTask()) {
			default: break;
			case 'list':
				$step = (isset($_POST['step'])) ? $_POST['step'] : 0;
				
				$blog_entries = \Ode\DBO::getInstance()->query("
					SELECT a.*
					FROM db125612_blog.wp_posts AS a
					WHERE a.post_type = 'post'
					AND a.post_status = 'publish'
					ORDER BY a.post_date
					DESC
					LIMIT " . $step . ",10
				")->fetchAll(PDO::FETCH_OBJ);
					
				Util::json($blog_entries);
				exit();
				break;
			case 'search':
				$qry = "%" . preg_replace("/[\n\r\W\s]+/", "%", trim($_POST['q'])) . "%";
				
				$sql = "
					SELECT a.*
					FROM db125612_blog.wp_posts AS a
					LEFT JOIN db125612_blog.wp_postmeta AS b ON (b.post_id = a.ID)
					WHERE (a.post_title LIKE " . \Ode\DBO::getInstance()->quote($qry) . "
					OR a.post_excerpt LIKE " . \Ode\DBO::getInstance()->quote($qry) . "
					OR a.post_content LIKE " . \Ode\DBO::getInstance()->quote($qry) . ")
					AND a.post_type = 'post'
					AND b.meta_key = 'alloc'
					AND b.meta_value = 'the_loud_minority'
					AND a.post_status = 'publish'
				";
				//echo $sql;
				
				$posts = \Ode\DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_OBJ);
				
				Util::json($posts);
				exit();
				break;
		}
		break;
	case 'logs':
		switch(\Ode\Manager::getInstance()->getTask()) {
			default: break;
			case 'list':
				$step = (isset($_POST['step'])) ? intval(trim($_POST['step'])) : 0;
				
				$logs = \Ode\DBO::getInstance()->query("
					SELECT a.*
					FROM hflogs AS a
					ORDER BY a.time_on
					DESC
					LIMIT ".$step.",10
				")->fetchAll(PDO::FETCH_CLASS, "Ode_DBO_Hflog_Model");
				
				\Ode\View::getInstance()->assign("logs", $logs);
				
				header("Content-Type: text/html");
				echo \Ode\View::getInstance()->fetch("ajax/mobile/log-list-view.tpl.php");
				exit();
				break;
		}
		break;
}
?>