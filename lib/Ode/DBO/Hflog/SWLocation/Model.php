<?php
namespace Ode\DBO\Hflog\SWLocation;

use Ode\DBO\SWLocation;

class Model {
	public $id;
	public $sw_loc_id;
	public $hflog_id;
	
	public function location() {
		return SWLocation::getOneById($this->sw_loc_id);
	}
}