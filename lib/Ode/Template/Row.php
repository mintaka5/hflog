<?php
class Ode_Template_Row extends ArrayObject {
	public function __construct() {
		$args = func_get_args();
	}
	
	public function __set($name, $value) {
		$object = new stdClass();
		$object->name = $name;
		$object->value = $value;
		
		$this->offsetSet($name, $object);
	}
	
	public function __get($name) {
		return $this->offsetGet($name);
	}
}