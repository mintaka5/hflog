<?php
namespace Ode\Geo;

/**
 * Geographical coordinate object
 * 
 * @author cjwalsh
 * @copyright Christopher Walsh
 * @package Ode_Geo
 * @name LatLng
 *
 */
class LatLng implements \JsonSerializable
{
	/**
	 * 
	 * @var float
	 * @access private
	 */
	private $lat = null;
	
	/**
	 * 
	 * @var float
	 * @access private
	 */
	private $lon = null;
	
	/**
	 * Constructor
	 * 
	 * @param float $lat
	 * @param float $lon
	 */
	public function __construct($lat = null, $lon = null)
	{
		$this->setLat($lat);
		$this->setLon($lon);
	}
	
	public function __toString() {
		return (string) round($this->lat(), 2) . ", " . (string) round($this->lng(), 2);
	}
	
	/**
	 * Sets latitude in decimal degrees
	 * 
	 * @param float $lat
	 * @access public
	 * @return void
	 */
	public function setLat($lat)
	{
		$this->lat = $lat;
	}
	
	/**
	 * Retrieves latitude
	 * 
	 * @return float
	 * @access public
	 */
	public function lat()
	{
		return $this->lat;
	}
	
	/**
	 * Sets longitude in decimal degrees
	 * 
	 * @param float $lon
	 * @access public
	 * @return void
	 */
	public function setLon($lon)
	{
		$this->lon = $lon;
	}
	
	/**
	 * Retrieve longitude
	 * 
	 * @access public
	 * @return float
	 */
	public function lng()
	{
		return $this->lon;
	}
	
	/**
	 * Checks to see if coordinate is valid
	 * by containing both latitude and longitude
	 * 
	 * @return boolean
	 * @access public
	 */
	public function isValid()
	{
		if(!is_null($this->lng()) || !is_null($this->lat())) {
			return true;
		} else {
			return false;
		}
	}
	
	public function toDegrees() {
		$str = "";
		
		if($this->isValid()) {
			$latCard = ($this->lat() > 0) ? 'N' : 'S';
			$lngCard = ($this->lng() > 0) ? 'E' : 'W';
			
			$lat = Util::decimalToDegrees($this->lat());
			$lng = Util::decimalToDegrees($this->lng());
			
			$str .= $lat['deg'] . "&deg; " . $lat['min'] . "' " . $lat['sec'] . "&quot; " . $latCard;
			$str .= ", " . $lng['deg'] . "&deg; " . $lng['min'] . "' " . $lng['sec'] . "&quot; " . $lngCard;

		}
		
		return $str;
	}

	public function toCoordinate() {
		return $this->lat() . ',' . $this->lng();
	}

	/**
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	function jsonSerialize()
	{
		return array(
			'lat' => $this->lat(),
			'lng' => $this->lng()
		);
	}
}
