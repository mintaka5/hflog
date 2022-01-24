<?php
namespace Ode\Geo;

class Util {
	const EARTH_RADIUS_METERS = 6378137.0;
	
	public static function toRad($degree) {
		return $degree * pi() / 180;
	}
	
	public static function toDegree($radian) {
		return $radian * 180 / pi();
	}
	
	public static function toDecimalDegrees($degrees, $minutes, $seconds = null) {
		$secs = (is_null($seconds)) ? 0 : $seconds;
		
		$fractional_part = (($minutes * 60) + $secs) / 3600;
		
		if((float)$degrees < 0) {
			$deg = floatval($degrees - $fractional_part);
		} else {
			$deg = floatval($degrees + $fractional_part);
		}
		
		return sprintf("%.5f", $deg);
	}
	
	public static function decimalToDegrees($decimal) {
		$dec = abs($decimal);
		$d = (int) $dec;
		$m = (int) (($dec - $d) * 60);
		$s = (int) ($dec - $d - ($m / 60)) * 3600;
		$s = round($s, 1);
		
		$array = array("deg" => $d, "min" => $m, "sec" => $s);
		
		return $array;
	}
}