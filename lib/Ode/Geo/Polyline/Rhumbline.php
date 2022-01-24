<?php
namespace Ode\Geo\Polyline;

use Ode\Geo\Polyline;
use Ode\Geo\Util;

class Rhumbline extends Polyline {
	private $_segements = 32;
	private $_start;
	private $_end;
	
	public function __construct(Ode_Geo_LatLng $start, Ode_Geo_LatLng $end) {
		$this->setStart($start);
		$this->setEnd($end);
	}
	
	private function setStart(Ode_Geo_LatLng $coord) {
		$this->_start = $coord;
	}
	
	private function getStart() {
		return $this->_start;
	}
	
	private function getEnd() {
		return $this->_end;
	}
	
	private function setEnd(Ode_Geo_LatLng $coord) {
		$this->_end = $coord;
	}
	
	public function getPlots() {
		$plots = array();
		
		$lat1 = Util::toRad($this->getStart()->lat());
		$lng1 = Util::toRad($this->getStart()->lng());
		
		$lat2 = Util::toRad($this->getEnd()->lat());
		$lng2 = Util::toRad($this->getEnd()->lng());
		
		$d = 2 * asin(sqrt(pow((sin(($lat1 - $lat2) / 2)), 2) + cos($lat1) * cos($lat2) * pow((sin(($lng1 - $lng2) / 2)), 2)));
	}
}