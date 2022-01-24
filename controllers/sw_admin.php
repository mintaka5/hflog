<?php
ini_set("display_errors", false);
error_reporting(E_ALL);

if(!\Ode\Auth::getInstance()->isAuth()) {
	header("Location: " . \Ode\Manager::getInstance()->friendlyAction(""));
	exit();
}

if(!\Ode\Auth::getInstance()->isAdmin()) {
	header("Location: " . \Ode\Manager::getInstance()->friendlyAction(""));
	exit();
}

switch(\Ode\Manager::getInstance()->getMode()) {
	default:
		switch(\Ode\Manager::getInstance()->getTask()) {
			default:
				$req = new HTTP_Request2("http://www.short-wave.info/", HTTP_Request2::METHOD_GET);
				$req->getUrl()->setQueryVariable("station", trim($_GET['stn']));
				$req->setHeader("User-Agent", DEFAULT_USER_AGENT);
				
				if($res = $req->send()) {
					$doc = new DOMDocument("1.0", "UTF-8");
					$doc->loadHTML($res->getBody());
					
					$xpath = new DOMXPath($doc);
					
					$freqColl = new ArrayObject();
					
					$freqNodes = $xpath->query("/html/body//table[@class='xb7']/tr[position()>3]");
					foreach($freqNodes as $freqNode) {
						$freq = $xpath->query("td[position()=1]/a", $freqNode)->item(0)->textContent;
						
						if(!empty($freq)) {
							$freqObj = new stdClass();
							$freqObj->frequency = $freq;
							
							$station_name = $xpath->query("td[position()=2]/a", $freqNode)->item(0)->textContent;
							$freqObj->stationTitle = $station_name;
							
							$station_name = preg_replace("/[\s\t\r\n\W]/", "", $station_name);
							$station_name = strtolower($station_name);
							$station_name = substr($station_name, 0, 12);
							$freqObj->stationName = $station_name;
							
							$freqObj->startTime = $xpath->query("td[position()=3]", $freqNode)->item(0)->textContent;
							
							$freqObj->endTime = $xpath->query("td[position()=4]", $freqNode)->item(0)->textContent;
							
							$freqObj->daysOfWeek = $xpath->query("td[position()=5]", $freqNode)->item(0)->textContent;
							
							$freqObj->language = $xpath->query("td[position()=6]/a", $freqNode)->item(0)->textContent;
							
							$freqObj->power = $xpath->query("td[position()=7]", $freqNode)->item(0)->textContent;
							
							$freqObj->azimuth = $xpath->query("td[position()=8]", $freqNode)->item(0)->textContent;
							
							$freqObj->txSite = $xpath->query("td[position()=9]", $freqNode)->item(0)->textContent;
							
							$latLng = $xpath->query("td[position()=10]", $freqNode)->item(0)->textContent;
							//$latLng = preg_grep("/(\d{2}([NS])\d{2}).*(\d{3}([EW])\d{2})/", $latLng);
							$latLng = iconv("UTF-8", "ISO-8859-1", $latLng);
							$latLng = html_entity_decode($latLng);
							preg_match("/(\d{2})([NS])(\d{2}).*(\d{3})([EW])(\d{2})/", $latLng, $matches);
							$latLng = $matches;
							
							$latAry['degs'] = (int)($latLng[2] == "S") ? -($latLng[1]) : $latLng[1];
							$latAry['mins'] = (int)$latLng[3];
							
							$lngAry['degs'] = (int)($latLng[5] == "W") ? -($latLng[4]) : $latLng[4];
							$lngAry['mins'] = (int)$latLng[6];
							
							$lat = \Ode\Geo\Util::toDecimalDegrees($latAry['degs'], $latAry['mins']);
							$lng = \Ode\Geo\Util::toDecimalDegrees($lngAry['degs'], $lngAry['mins']);
							
							$freqObj->coords = new \Ode\Geo\LatLng($lat, $lng);
							
							$freqColl->append($freqObj);
						}
					}
					
					//Util::debug($freqColl);
					$iter = $freqColl->getIterator();
					while($iter->valid()) {
						$station = \Ode\DBO::getInstance()->query("
							SELECT a.*
							FROM sw_stations AS a
							WHERE a.title = " . \Ode\DBO::getInstance()->quote($iter->current()->stationTitle, PDO::PARAM_STR) . "
							LIMIT 0,1
						")->fetchObject();
						
						$language = \Ode\DBO::getInstance()->query("
							SELECT a.*
							FROM languages AS a
							WHERE a.language = " . \Ode\DBO::getInstance()->quote($iter->current()->language, PDO::PARAM_STR) . "
							LIMIT 0,1
						")->fetchObject();
						
						if($station != false) {
							\Ode\DBO::getInstance()->beginTransaction();
							
							$sth = \Ode\DBO::getInstance()->prepare("
								INSERT INTO sw_locations (
									station_id, lat, lng, azimuth, site, start_utc, end_utc,
									days, frequency, power, lang_iso
								) VALUES (
									:a, :b, :c, :d, :e, :f, :g,
									:h, :i, :j, :k
								)");
							$sth->bindValue(":a", $station->id, PDO::PARAM_STR);
							$sth->bindValue(":b", floatval($iter->current()->coords->lat()), PDO::PARAM_INT);
							$sth->bindValue(":c", floatval($iter->current()->coords->lng()), PDO::PARAM_INT);
							$sth->bindValue(":d", intval($iter->current()->azimuth), PDO::PARAM_INT);
							$sth->bindValue(":e", $iter->current()->txSite, PDO::PARAM_STR);
							$sth->bindValue(":f", $iter->current()->startTime, PDO::PARAM_STR);
							$sth->bindValue(":g", $iter->current()->endTime, PDO::PARAM_STR);
							$sth->bindValue(":h", $iter->current()->daysOfWeek, PDO::PARAM_STR);
							$sth->bindValue(":i", $iter->current()->frequency, PDO::PARAM_INT);
							$sth->bindValue(":j", $iter->current()->power, PDO::PARAM_INT);
							$sth->bindValue(":k", ($language != false) ? $language->iso : null, ($language != false) ? PDO::PARAM_STR : PDO::PARAM_NULL);
							
							try {
								$sth->execute();
							} catch(\PDOException $e) {
								error_log($e->getTraceAsString(), 0);
							}
							
							try {
								\Ode\DBO::getInstance()->commit();
							} catch(\PDOException $e) {
								error_log($e->getTraceAsString());
							}
						} else {
							\Ode\DBO::getInstance()->beginTransaction();
							
							$uuid = UUID::get();
							
							$sth = \Ode\DBO::getInstance()->prepare("
								INSERT INTO sw_stations (id, station_name, title)
								VALUES (:a, :b, :c)
							");
							$sth->bindValue(":a", $uuid, PDO::PARAM_STR);
							$sth->bindValue(":b", $iter->current()->stationName, PDO::PARAM_STR);
							$sth->bindValue(":c", $iter->current()->stationTitle, PDO::PARAM_STR);
							
							try {
								$sth->execute();
							} catch(\PDOException $e) {
								Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
							}
							
							$sth = \Ode\DBO::getInstance()->prepare("
								INSERT INTO sw_locations (
									station_id, lat, lng, azimuth, site, start_utc, end_utc,
									days, frequency, power, lang_iso
								) VALUES (
									:a, :b, :c, :d, :e, :f, :g,
									:h, :i, :j, :k
								)");
							$sth->bindValue(":a", $uuid, PDO::PARAM_STR);
							$sth->bindValue(":b", floatval($iter->current()->coords->lat()), PDO::PARAM_INT);
							$sth->bindValue(":c", floatval($iter->current()->coords->lng()), PDO::PARAM_INT);
							$sth->bindValue(":d", intval($iter->current()->azimuth), PDO::PARAM_INT);
							$sth->bindValue(":e", $iter->current()->txSite, PDO::PARAM_STR);
							$sth->bindValue(":f", $iter->current()->startTime, PDO::PARAM_STR);
							$sth->bindValue(":g", $iter->current()->endTime, PDO::PARAM_STR);
							$sth->bindValue(":h", $iter->current()->daysOfWeek, PDO::PARAM_STR);
							$sth->bindValue(":i", $iter->current()->frequency, PDO::PARAM_INT);
							$sth->bindValue(":j", $iter->current()->power, PDO::PARAM_INT);
							$sth->bindValue(":k", ($language != false) ? $language->iso : null, ($language != false) ? PDO::PARAM_STR : PDO::PARAM_NULL);
						}
						
						try {
							$sth->execute();
						} catch(\PDOException $e) {
							error_log($e->getTraceAsString(), 0);
						}
						
						try {
							\Ode\DBO::getInstance()->commit();
						} catch(\PDOException $e) {
							error_log($e->getTraceAsString(), 0);
						}
						
						$iter->next();
					}
				}
				
				break;
		}
		break;
	case 'add':
		switch(\Ode\Manager::getInstance()->getTask()) {
			default:
				$form = new HTML_QuickForm2("addSW");
				$form->setAttribute("action", \Ode\Manager::getInstance()->action("sw_admin", "add"));
				
				$stationSel = $form->addSelect("stationId")->setLabel("Station");
				$stations = \Ode\DBO::getInstance()->query("
					SELECT a.id, a.title
					FROM sw_stations AS a
					ORDER BY a.title
					ASC
				")->fetchAll(PDO::FETCH_OBJ);
				foreach($stations as $station) {
					$stationSel->addOption($station->title, $station->id);
				}
				
				/*$tmpLat = $form->addText("lat")->setLabel("Latitude");
				$tmpLat->addRule("required", "Required");
				
				$tmpLng = $form->addText("lng")->setLabel("Longitude");
				$tmpLng->addRule("required", "Required");*/
				$tmpCoord = $form->addText("coords")->setLabel("Coordinates");
				$tmpCoord->addRule("required", "Required");
				$tmpCoord->addRule("regex", "Not a valid format", "/\d{2}[NS]\d{2}\s\d{3}[EW]\d{2}/");
				
				$startTxt = $form->addText("start")->setLabel("Start time UTC");
				$startTxt->addRule("required", "Required");
				$startTxt->addRule("regex", "Not a valid format (i.e. hh:mm)", "/\d{1,2}\:\d{2}/");
				
				$endTxt = $form->addText("end")->setLabel("End time");
				$endTxt->addRule("required", "Required");
				$endTxt->addRule("regex", "Not a valid format (i.e. hh:mm)", "/\d{1,2}\:\d{2}/");
				
				$freqTxt = $form->addText("freq")->setLabel("Frequency (kHz)");
				$freqTxt->addRule("required", "Required");
				$freqTxt->addRule("regex", "Not a valid format (i.e. 5616.00)", "/\d{2,6}(\.\d{2})?/");
				
				$pwrTxt = $form->addText("pwr")->setLabel("Power (watts)");
				$pwrTxt->addRUle("required", "Required");
				$pwrTxt->addRule("regex", "Up to 6 numbers only", "/\d{1,6}/");
				
				$langSel = $form->addSelect("lang")->setLabel("Language");
				$langs = \Ode\DBO::getInstance()->query("SELECT a.* FROM languages AS a ORDER BY a.language ASC")->fetchAll(PDO::FETCH_OBJ);
				foreach($langs as $lang) {
					$langSel->addOption($lang->language, $lang->iso);
				}
				$langSel->setValue("eng");
				
				$btn = $form->addSubmit("submit")->setAttribute("value", "Add");
				
				if($form->validate()) {
					Util::debug($_POST);
				}
				
				\Ode\View::getInstance()->assign("form", $form->render(\Ode\View::getInstance()->getFormRenderer()));
				break;
		}
		break;
}

$view->display("sw_admin.tpl.php");
exit();
?>