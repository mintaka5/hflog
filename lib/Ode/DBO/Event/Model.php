<?php
namespace Ode\DBO\Event;

class Model {
	public $id;
	public $title;
	public $summary;
	public $starts;
	public $ends;
	public $is_active;
	public $is_deleted;
	public $created;
	public $modified;
	
	public function categories() {
		return Ode_DBO_Event_Category::getAllByEventID($this->id);
	}
	
	public function location() {
		
	}
}