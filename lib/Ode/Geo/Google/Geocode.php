<?php
namespace Ode\Geo\Google;

use Ode\Geo\LatLng;
use Ode\Geo\LatLngBounds;

class Geocode
{
	const API_XML_URL = "http://maps.googleapis.com/maps/api/geocode/xml";
	const API_JSON_URL = 'http://maps.googleapis.com/maps/api/geocode/json';
	
	public static function fromLatLng(LatLng $coords) {
		$req = new \HTTP_Request2(self::API_XML_URL, \HTTP_Request2::METHOD_GET);
		$req->getUrl()->setQueryVariable("latlng", $coords->lat() . "," . $coords->lng());
		$req->getUrl()->setQueryVariable("sensor", "false");
		
		$address = "";
		
		$res = $req->send();
		if($res->getStatus() == 200) {
			$doc = new \DOMDocument();
			$doc->loadXML($res->getBody());
			
			/*header("Content-Type: text/xml");
			echo $res->getBody();
			exit();*/
			
			$xpath = new \DOMXPath($doc);
			
			/**
			 * Address string
			 * @todo capability to select various approximations (i.e. 'postal code' or 'route')
			 * @var string
			 */
			$address = $xpath->query("//result/formatted_address")->item(0)->textContent;
		}
		
		return $address;
	}

	public static function fromLatLngAdminLevel(LatLng $coords) {
		// /*/result/type[contains(., 'administrative_area_level_3') or contains(., 'administrative_area_level_4') or contains(., 'administrative_area_level_2')]/../formatted_address/text()
		$req = new \HTTP_Request2(self::API_XML_URL, \HTTP_Request2::METHOD_GET);
		$req->getUrl()->setQueryVariable("latlng", $coords->lat() . "," . $coords->lng());
		$req->getUrl()->setQueryVariable("sensor", "false");

		$address = "";

		$res = $req->send();
		if($res->getStatus() == 200) {
			$doc = new \DOMDocument();
			$doc->loadXML($res->getBody());

			/*header("Content-Type: text/xml");
			echo $res->getBody();
			exit();*/

			$xpath = new \DOMXPath($doc);

			/**
			 * Address string
			 * @todo capability to select various approximations (i.e. 'postal code' or 'route')
			 * @var string
			 */
			$address = $xpath->query("/*/result/type[contains(., 'administrative_area_level_3') or contains(., 'administrative_area_level_4') or contains(., 'administrative_area_level_2')]/../formatted_address/text()")->item(0)->textContent;
		}

		return $address;
	}
	
	public static function fromAddress($address) {
		$req = new \HTTP_Request2(self::API_XML_URL, \HTTP_Request2::METHOD_GET);
		$req->getUrl()->setQueryVariable("address", trim($address));
		$req->getUrl()->setQueryVariable("sensor", "false");
		
		$obj = new \stdClass();
		
		$res = $req->send();
		if($res->getStatus() == 200) {
			$doc = new \DOMDocument();
			$doc->loadXML($res->getBody());
			
			$xpath = new \DOMXPath($doc);
			
			$obj->address = $xpath->query("//result/formatted_address")->item(0)->textContent;
			
			$obj->streetNumber = $xpath->query("//result/address_component/type[.='street_number']/../long_name")->item(0)->textContent;
			$obj->streetName = $xpath->query("//result/address_component/type[.='route']/../long_name")->item(0)->textContent;
			$obj->city = $xpath->query("//result/address_component/type[.='administrative_area_level_3']/../long_name")->item(0)->textContent;
			$obj->state = $xpath->query("//result/address_component/type[.='administrative_area_level_1']/../short_name")->item(0)->textContent;
			$obj->postalCode = $xpath->query("//result/address_component/type[.='postal_code']/../long_name")->item(0)->textContent;
			
			$lat = $xpath->query("//result/geometry/location/lat")->item(0)->textContent;
			$lng = $xpath->query("//result/geometry/location/lng")->item(0)->textContent;
			$obj->center = new Ode_Geo_LatLng($lat, $lng);
			
			$swLat = $xpath->query("//result/geometry/viewport/southwest/lat")->item(0)->textContent;
			$swLng = $xpath->query("//result/geometry/viewport/southwest/lng")->item(0)->textContent;
			$swLatLng = new Ode_Geo_LatLng($swLat, $swLng);
			
			$neLat = $xpath->query("//result/geometry/viewport/northeast/lat")->item(0)->textContent;
			$neLng = $xpath->query("//result/geometry/viewport/northeast/lng")->item(0)->textContent;
			$neLatLng = new Ode_Geo_LatLng($neLat, $neLng);
			
			$obj->bounds = new Ode_Geo_LatLngBounds($swLatLng, $neLatLng);
		}
		
		return $obj;
	}
	
	/**
	 * Retrieve well-formed address string from Google's geocoding
	 * service.
	 * @param LatLng $coords
	 * @return string
	 * @deprecated
	 */
	public static function addressfromLatLng(LatLng $coords)
	{
		$req = new \HTTP_Request();
		$req->setURL('http://maps.google.com/maps/geo');
		$req->addHeader('User-Agent', DEFAULT_USER_AGENT);
		$req->addQueryString('q', $coords->lat() . ',' . $coords->lng(), true);
		$req->addQueryString('output', 'json');
		$req->addQueryString('oe', 'utf8');
		$req->addQueryString('sensor', 'false');
		$req->addQueryString('key', GOOGLE_API_KEY);
		
		$address = "";
		
		if($req->sendRequest()) {
			$res = json_decode($req->getResponseBody(), true);
			//Misc::debug($res);
			if(!empty($res['Placemark'])) {
				$address = $res['Placemark'][0]['address'];
			}
		}
		
		return $address;
	}
	
	/**
	 * Retrieves coordinates and bounding box
	 * from Google's geocoding service
	 * @param string $address
	 * @return stdClass
	 * @deprecated
	 */
	public static function latLngfromAddress($address)
	{
		$lat = null;
		$lon = null;
		$coords = false;
		
		$request = new \HTTP_Request();
		$request->addHeader('User-Agent', DEFAULT_USER_AGENT);
		$request->setURL('http://maps.google.com/maps/geo');
		$request->addQueryString('oe', 'utf8');
		$request->addQueryString('output', 'xml');
		$request->addQueryString('sensor', 'false');
		$request->addQueryString('key', GOOGLE_API_KEY);
		$request->addQueryString('q', trim($address));
		
		$obj = new \stdClass();
		if($request->sendRequest())
		{
			$response = $request->getResponseBody();
			
			$doc = new \DOMDocument();
			$doc->loadXML($response);
			
			$coordStr = $doc->getElementsByTagName('Placemark')->item(0)->getElementsByTagName('Point')->item(0)->getElementsByTagName('coordinates')->item(0)->firstChild->nodeValue;
			$coordAry = explode(',', $coordStr);
			$lat = $coordAry[1];
			$lon = $coordAry[0];
			
			$latlonboxNode = $doc->getElementsByTagName('Placemark')->item(0)->getElementsByTagName('ExtendedData')->item(0)->getElementsByTagName('LatLonBox')->item(0);
			$swx = $latlonboxNode->getAttribute('west');
			$swy = $latlonboxNode->getAttribute('south');
			$nex = $latlonboxNode->getAttribute('east');
			$ney = $latlonboxNode->getAttribute('north');
			
			$countryCode = trim($doc->getElementsByTagName('Placemark')->item(0)->getElementsByTagName('CountryNameCode')->item(0)->firstChild->nodeValue);
			
			$center = new LatLng($lat, $lon);
			$bbox = new LatLngBounds(new LatLng($swy, $swx), new LatLng($ney, $nex));
			
			$obj->countryCode = $countryCode;
			$obj->center = $center;
			$obj->bounds = $bbox;
		}
		
		return $obj;
	}
}
