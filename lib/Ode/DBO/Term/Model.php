<?php
namespace Ode\DBO\Term;

class Model {
	public $name;
	public $object_id;
	
	public function post() {
		return \Ode\DBO::getInstance()->query("
			SELECT a.*
			FROM db125612_blog.wp_posts AS a
			WHERE a.ID = " . \Ode\DBO::getInstance()->quote($this->object_id, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject(PDO::FETCH_CLASS, "Ode_DBO_Post_Model");
	}
}
?>