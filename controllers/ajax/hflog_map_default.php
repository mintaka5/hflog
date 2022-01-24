<?php
require_once './init.php';

switch(\Ode\Manager::getInstance()->getMode()) {
	default:
		$sql = "SELECT
				d.title AS station,
				c.site AS site,
				a.description AS log_description,
				c.lat AS location_lat,
				c.lng AS location_lng
			FROM hflogs AS a
			LEFT JOIN hflog_sw_locations AS b ON (b.hflog_id = a.id)
			LEFT JOIN sw_locations AS c ON (c.id = b.sw_loc_id)
			LEFT JOIN sw_stations AS d ON (d.id = c.station_id)
			WHERE b.id IS NOT NULL";
		
		$logs = \Ode\DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		
		$json = new Services_JSON();
		echo $json->encode(array("sites" => $logs));
		exit();
		break;
}
?>