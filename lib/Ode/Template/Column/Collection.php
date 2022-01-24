<?php
class Ode_Template_Column_Collection extends ArrayObject {
	public function __construct() {
		$args = func_get_args();
		
		if(!empty($args)) {
			foreach($args as $num => $object) {
				$this->add($num, $object);
			}
		}
	}
	
	public function add($id, Ode_Template_Column $object) {
		$this->offsetSet($id, $object);
	}
	
	public function get($id) {
		return $this->offsetGet($id);
	}
}