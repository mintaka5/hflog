<?php
namespace Ode\DBO\Group;

class Model {
	public $id;
	public $title;
	public $description;
	
	public function frequencies() {
		return Ode_DBO_Frequency::getAllByGroup($this->id);
	}
	
	public function county() {
		return Ode_DBO_County::getOneByGroup($this->id);
	}
	
	public function title($default = "No title") {
		if(empty($this->title)) {
			return $default;
		}
		
		return $this->title;
	}
	
	public function description($default = "No description") {
		if(empty($this->description)) {
			return $default;
		}
	
		return $this->description;
	}
}
?>