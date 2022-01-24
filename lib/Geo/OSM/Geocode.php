<?php
namespace Geo\OSM;
use \HTTP_Request2 as HTTP_Request2;

class Geocode {
    const REVERSE_URL = 'http://nominatim.openstreetmap.org/reverse';
    const FORWARD_URL = 'http://nominatim.openstreetmap.org/search';

    private $request;
    private $coordinates;

    /**
     * @return \Ode_Geo_LatLng
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * @param \Ode_Geo_LatLng $coordinates
     */
    public function setCoordinates(\Ode_Geo_LatLng $coordinates)
    {
        $this->coordinates = $coordinates;
    }

    public function __construct() {

    }

    /**
     * @return HTTP_Request2
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param HTTP_Request2 $request
     * @return \stdClass|boolean
     */
    public function setRequest(HTTP_Request2 $request)
    {
        $this->request = $request;
    }

    public static function reverse(\Ode_Geo_LatLng $coords) {
        $geocoder = new Geocode();
        $geocoder->setCoordinates($coords);

        $geocoder->setRequest(new HTTP_Request2(self::REVERSE_URL, HTTP_Request2::METHOD_GET));
        $geocoder->getRequest()->getUrl()->setQueryVariable('format', 'json');
        $geocoder->getRequest()->getUrl()->setQueryVariable('lat', $coords->lat());
        $geocoder->getRequest()->getUrl()->setQueryVariable('lon', $coords->lng());
        $geocoder->getRequest()->getUrl()->setQueryVariable('addressdetails', strval(1));

        $result = $geocoder->getRequest()->send();
        if($result->getStatus() == 200) {
            $data = json_decode($result->getBody());

            return $data;
        }

        return false;
    }
}