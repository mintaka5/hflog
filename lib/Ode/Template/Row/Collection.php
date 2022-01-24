<?php
class Ode_Template_Row_Collection extends ArrayObject {
	public function __construct() {
		$args = func_get_args();
		
		foreach($args as $row) {
			if($row instanceof Ode_Template_Row) {
				$this->add($row);
			}
		}
	}
	
	public function add(Ode_Template_Row $row) {
		$this->offsetSet(UUID::get(), $row);
	}
	
	public function get($id) {
		return $this->offsetGet($id);
	}
}