<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

@define("HOURLY_FILENAME_FORMAT", "hour_%s.txt");
@define("HOURLY_WEB_PATH", "http://www.ndbc.noaa.gov/data/hourly2/");
@define("REGEX_LINE_SPLIT", "/[\r\n]+/");

@define("HOURLY_STNID_INDEX", 0);
@define("HOURLY_YEAR_INDEX", 1);
@define("HOURLY_MONTH_INDEX", 2);
@define("HOURLY_DAY_INDEX", 3);
@define("HOURLY_HOUR_INDEX", 4);
@define("HOURLY_MINUTE_INDEX", 5);
@define("HOURLY_WDIR_INDEX", 6);
@define("HOURLY_WSPD_INDEX", 7);
@define("HOURLY_GST_INDEX", 8);
@define("HOURLY_WVHT_INDEX", 9);
@define("HOURLY_DPD_INDEX", 10);
@define("HOURLY_APD_INDEX", 11);
@define("HOURLY_MWD_INDEX", 12);
@define("HOURLY_PRES_INDEX", 13);
@define("HOURLY_ATMP_INDEX", 14);
@define("HOURLY_WTMP_INDEX", 15);
@define("HOURLY_DEWP_INDEX", 16);
@define("HOURLY_VIS_INDEX", 17);
@define("HOURLY_PTDY_INDEX", 18);
@define("HOURLY_TIDE_INDEX", 19);

class BuoyData_Hourly {
	private $station_id;
	private $datetime;
	
	/**
	 * 
	 * Wind direction
	 * @var integer
	 */
	private $wdir;
	
	/**
	 * 
	 * Wind speed
	 * @var float
	 */
	private $wspd;
	
	/**
	 * 
	 * Wind gusts
	 * @var float
	 */
	private $gusts;
	
	/**
	 *
	 * Wave height
	 * @var float
	 */
	private $wvht;
	
	/**
	 * 
	 * Dominant wave period
	 * @var float
	 */
	private $dpd;
	
	/**
	 * 
	 * Average wave period
	 * @var float
	 */
	private $apd;
	
	/**
	 *
	 * The direction from which the waves at the dominant period (DPD) are coming.
	 * @var integer
	 */
	private $mwd;
	
	/**
	 * 
	 * Sea level pressure
	 * @var float
	 */
	private $pres;
	
	/**
	 * 
	 * Air temperature (celsius)
	 * @var float
	 */
	private $atmp;
	
	/**
	 * 
	 * Sea surface temperature (celsius)
	 * @var float
	 */
	private $wtmp;
	
	/**
	 * 
	 * Dewpoint temperature (celsius)
	 * @var float
	 */
	private $dewp;
	
	/**
	 * 
	 * Station visibility (nautical miles)
	 * @var float
	 */
	private $vis;
	
	/**
	 * 
	 * Pressure tendency
	 * @var string
	 */
	private $ptdy;
	
	/**
	 * 
	 * The water level in feet above or below Mean Lower Low Water (MLLW).
	 * @var float
	 */
	private $tide;
	
	public function __construct() {}
	
	public function setStationId($id) {
		$this->station_id = $id;
	}
	
	public function getStationId() {
		return $this->station_id;
	}
	
	public function setDate(Date $date) {
		$this->datetime = $date;
	}
	
	public function getDate() {
		return $this->datetime;
	}
	
	public function setWindDirection($wdir) {
		$this->wdir = $wdir;
	}
	
	public function getWindDirection() {
		return $this->wdir;
	}
	
	public function setWindSpeed($spd) {
		$this->wspd = $spd;
	}
	
	public function getWindSpeed() {
		return $this->wspd;
	}
	
	public function setGusts($spd) {
		$this->gusts = $spd;
	}
	
	public function getGusts() {
		return $this->gusts;
	}
	
	public function setWaveHeight($hgt) {
		$this->wvht = $hgt;
	}
	
	public function getWaveHeight() {
		return $this->wvht;
	}
	
	public function setDominantWavePeriod($period) {
		$this->dpd = $period;
	}
	
	public function getDominantWavePeriod() {
		return $this->dpd;
	}
	
	public function setAverageWavePeriod($period) {
		$this->apd = $period;
	}
	
	public function getAverageWavePeriod() {
		return $this->apd;
	}
	
	public function setWaveDirection($dir) {
		$this->mwd = $dir;
	}
	
	public function getWaveDirection() {
		return $this->mwd;
	}
	
	public function setPressure($pres) {
		$this->pres = $pres;
	}
	
	public function getPressure() {
		return $this->pres;
	}
	
	public function setAirTemp($temp) {
		$this->atmp = $temp;
	}
	
	public function getAirTemp() {
		return $this->atmp;
	}
	
	public function setWaterTemp($temp) {
		$this->wtmp = $temp;
	}
	
	public function getWaterTemp() {
		return $this->wtmp;
	}
	
	public function setDewpoint($dewp) {
		$this->dewp = $dewp;
	}
	
	public function getDewpoint() {
		return $this->dewp;
	}
	
	public function setVisibility($vis) {
		$this->vis = $vis;
	}
	
	public function getVisibility() {
		return $this->vis;
	}
	
	public function setPressureTendency($ptdy) {
		$this->ptdy = $ptdy;
	}
	
	public function getPressureTendency() {
		return $this->ptdy;
	}
	
	public function setTide($tide) {
		$this->tide = $tide;
	}
	
	public function getTide() {
		return $this->tide;
	}
}

switch(\Ode\Manager::getInstance()->getMode()) {
	default: break;
	case 'hourly':
		$hourlyFile = acquireHourlyFile();
		
		parseHourlyData($hourlyFile);
		exit();
		break;
	case 'owners':
		$txt = File::readAll(APP_LIB_PATH.DIRECTORY_SEPARATOR."BuoyData/station_owners.txt");
	
		$lines = preg_split(REGEX_LINE_SPLIT, $txt);

		$collection = new ArrayObject();
		
		foreach($lines as $num => $row) {
			if(substr($row, 0, 1) != "#") {
				$cols = explode("|", $row);
				
				//Util::debug($cols);
				$owner_id = trim($cols[0]);
				$owner_name = trim($cols[1]);
				$country_code = trim($cols[2]);
				
				$owner = \Ode\DBO::getInstance()->query("
					SELECT a.id
					FROM buoy_owners AS a
					WHERE a.owner_id = " . \Ode\DBO::getInstance()->quote($owner_id, PDO::PARAM_STR) . "
					LIMIT 0,1
				")->fetchColumn();
				
				if($owner == false) {
					$sth = \Ode\DBO::getInstance()->prepare("
						INSERT INTO buoy_owners (owner_id, owner_name, country_code, created, modified)
						VALUES (:id, :name, :country, NOW(), NOW())
					");
					$sth->bindParam(":id", $owner_id, PDO::PARAM_STR, 45);
					$sth->bindParam(":name", $owner_name, PDO::PARAM_STR, 225);
					$sth->bindParam(":country", $country_code, PDO::PARAM_STR, 45);
					
					try {
						$sth->execute();
					} catch(PDOException $e) {
						exit($e->getTraceAsString());
					} catch(Exception $e) {
						exit($e->getTraceAsString());
					}
				}
			}
		}
		exit();
		break;
}

function parseHourlyData($filename) {
	$txt = File::readAll($filename);
	$lines = preg_split(REGEX_LINE_SPLIT, $txt);
	
	$collection = new ArrayObject();
	
	foreach($lines as $num => $row) {
		if(substr($row, 0, 1) != "#") {
			$cols = preg_split("/\s+/", $row);
			
			
		}
	}
}

function acquireHourlyFile() {
	$curHour = intval(gmdate("H"));
	$curHour = $curHour-1;
	$date = new Date();
	$date->setHour($curHour);
	$curHour = sprintf("%2d", strval($date->getHour()));
	
	$hourlyFile = sprintf(HOURLY_FILENAME_FORMAT, $curHour);
	
	$newFile = APP_LIB_PATH.DIRECTORY_SEPARATOR."BuoyData".DIRECTORY_SEPARATOR.$hourlyFile;
	
	if(!file_exists($newFile)) {
		$req = new HTTP_Request2(HOURLY_WEB_PATH.$hourlyFile, HTTP_Request2::METHOD_GET);
		
		if($req->send()) {
			$res = $req->send()->getBody();
			
			$handle = fopen($newFile, "w");
			if($handle) {
				chmod($newFile, 0777);
				
				$e = File::write($newFile, $res, FILE_MODE_WRITE);
				
				if(PEAR::isError($e)) {
					die($e->getMessage());
				}
			} else {
				die("File creation failed");
			}
		}
	}
	
	return $newFile;
}
?>