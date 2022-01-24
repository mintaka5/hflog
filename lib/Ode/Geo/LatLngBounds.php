<?php
namespace Ode\Geo;

/**
 * Bounding rectangle for two latitude/longitude coordinates
 * 
 * @author cjwalsh
 * @copyright Christopher Walsh 2010
 * @package Ode_Geo
 * @name LatLngBounds
 *
 */
class LatLngBounds
{
	/**
	 * 
	 * @var Ode_Geo_LatLng
	 * @access private
	 */
	private $southwest;
	
	/**
	 * 
	 * @var Ode_Geo_LatLng
	 * @access private
	 */
	private $northeast;
	
	/**
	 * Constructor
	 * 
	 * @param Ode_Geo_LatLng $sw
	 * @param Ode_Geo_LatLng $ne
	 * @access public
	 * @return void
	 */
	public function __construct(Ode_Geo_LatLng $sw = null, Ode_Geo_LatLng $ne = null)
	{
		if(!is_null($sw)) {
			$this->setSouthwest($sw);
		}
		
		if(!is_null($ne)) {
			$this->setNortheast($ne);
		}
	}
	
	public function setSouthwest(Ode_Geo_LatLng $latlng)
	{
		$this->southwest = $latlng;
	}
	
	public function setNortheast(Ode_Geo_LatLng $latlng)
	{
		$this->northeast = $latlng;
	}
	
	public function getSouthwest()
	{
		return $this->southwest;
	}
	
	public function getNortheast()
	{
		return $this->northeast;
	}
}
?>