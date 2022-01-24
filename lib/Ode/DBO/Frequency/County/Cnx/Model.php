<?php
namespace Ode\DBO\Frequency\County\Cnx;

class Model {
	public $id;
	public $county_id;
	public $frequency_id;
	
	public function frequency() {
		return Ode_DBO_Frequency::getOneById($this->frequency_id);
	}
	
	public function county() {
		return Ode_DBO_County::getOneById($this->county_id);
	}
}