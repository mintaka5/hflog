<?php
namespace Ode\DBO\Event\Location;

class Model {
	public $id;
	public $title;
	public $summary;
	public $street;
	public $city;
	public $state;
	public $zip;
	public $lat;
	public $lng;
	public $is_active;
	
	public function coordinates() {
		return new Ode_Geo_LatLng($this->lat, $this->lng);
	}
}