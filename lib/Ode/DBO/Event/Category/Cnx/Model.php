<?php
namespace Ode\DBO\Event\Category\Cnx;

class Model {
	public $id;
	public $event_id;
	public $category_id;
	
	public function event() {
		return Ode_DBO_Event::getOneByID($this->event_id);
	}
	
	public function category() {
		return Ode_DBO_Event_Category::getOneByID($this->category_id);
	}
}
?>