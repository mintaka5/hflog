<?php
namespace Ode\DBO\County;

class Model {
	public $cid;
	public $state;
	public $name;
	public $type;
	public $pop;
	
	public function frequencies() {
		return Ode_DBO_Frequency::getAllByCounty($this->cid);
	}
	
	public function groups() {
		return Ode_DBO_Group::getAllByCounty($this->cid);
	}
	
	public function state() {
		return \Ode\DBO::getInstance()->query("
			SELECT state.*
			FROM states AS state
			WHERE state.abbrev = " . \Ode\DBO::getInstance()->quote($this->state, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetch(PDO::FETCH_OBJ);
	}
}