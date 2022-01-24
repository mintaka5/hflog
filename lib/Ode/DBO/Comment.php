<?php
namespace Ode\DBO;

class Comment {
	public static function getAllByPost($post_id) {
		return \Ode\DBO::getInstance()->query("
			SELECT a.*
			FROM db125612_blog.wp_comments AS a
			WHERE a.comment_post_ID = " . \Ode\DBO::getInstance()->quote($post_id, PDO::PARAM_INT) . "
			AND a.comment_approved = 1
			ORDER BY a.comment_date_gmt
			DESC
		")->fetchAll(\PDO::FETCH_CLASS, "Ode_DBO_Comment_Model");
	}
}