<?php
require_once './init.php';

$locations = \Ode\DBO::getInstance()->query("
	SELECT
		a.id, a.site, REPLACE(FORMAT(a.frequency, 2), ',', '') AS frequency,
		CONCAT(IF(a.start_utc, TIME_FORMAT(a.start_utc, '%H:%i'), ''), IF(a.end_utc, ' - ' , ''), IF(a.end_utc, TIME_FORMAT(a.end_utc, '%H:%i'), '')) AS time_slot,
		b.language
	FROM " . \Ode\DBO\SWLocation::TABLE_NAME . " AS a
	LEFT JOIN " . \Ode\DBO\Language::TABLE_NAME . " AS b ON (b.iso = a.lang_iso)
	WHERE station_id = " . \Ode\DBO::getInstance()->quote($_POST['stnId'], PDO::PARAM_STR) . "
	ORDER BY a.site
")->fetchAll(PDO::FETCH_ASSOC);

Util::json($locations);
exit();
?>