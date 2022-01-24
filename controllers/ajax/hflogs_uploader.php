<?php
require_once './init.php';

@define("DS", DIRECTORY_SEPARATOR);

$audioPath = $_SERVER['DOCUMENT_ROOT'];
$fullAudioPath = $audioPath . DS . "audio" . DS . "stations" . DS;

$databasePath = "/audio/stations/" . date("Y") . "/" . date("m") . "/" . date("d") . "/";

$fileName = $_FILES['file']['name'];
$fileName = preg_replace("/[^\w\._]+/", "_", $fileName);

$databaseName = $databasePath . $fileName;

if(!file_exists($fullAudioPath . date("Y"))) {
	mkdir($fullAudioPath . date("Y"));
}

if(!file_exists($fullAudioPath . date("Y") . DS . date("m"))) {
	mkdir($fullAudioPath . date("Y") . DS . date("m"));
}

if(!file_exists($fullAudioPath . date("Y") . DS . date("m") . DS . date("d"))) {
	mkdir($fullAudioPath . date("Y") . DS . date("m") . DS . date("d"));
}

$targetName = $audioPath . $databasePath . $fileName;

if(move_uploaded_file($_FILES['file']['tmp_name'], $targetName)) {
	$pathInfo = pathinfo($targetName);
	
	\Ode\DBO::getInstance()->beginTransaction();
	
	$sth = \Ode\DBO::getInstance()->prepare("INSERT INTO sw_audio (filename, title) VALUES (:filename, :title)");
	$sth->bindParam(":filename", $databaseName, PDO::PARAM_STR);
	$sth->bindParam(":title", str_replace("_", " ", $pathInfo['filename']), PDO::PARAM_STR, 255);
	
	$sth->execute();
	
	$new_id = \Ode\DBO::getInstance()->lastInsertId();
	
	unset($sth);
	
	$sth = \Ode\DBO::getInstance()->prepare("INSERT INTO sw_station_audio_cnx (station_id, audio_id) VALUES (:station, :audio)");
	$sth->bindParam(":station", $_REQUEST['station_id'], PDO::PARAM_STR, 50);
	$sth->bindParam(":audio", $new_id, PDO::PARAM_INT, 11);
	
	$sth->execute();
	
	\Ode\DBO::getInstance()->commit();
	
	/*$sth = \Ode\DBO::getInstance()->prepare("
		INSERT INTO " . Ode_DBO_SWStation_Audio::TABLE_NAME . " (filename, station_id, title)
		VALUES(:filename, :station, :title)
	");
	$sth->bindParam(":filename", $databaseName, PDO::PARAM_STR, 255);
	$sth->bindParam(":station", $_REQUEST['station_id'], PDO::PARAM_STR, 50);
	$sth->bindParam(":title", str_replace("_", " ", $pathInfo['filename']), PDO::PARAM_STR, 45);
	
	try {
		$sth->execute();
	} catch(PDOException $e) {
		//error_log($e->getMessage(), 1, "cjwalsh@ymail.com");
		Util::json($e->getMessage());
	} catch(Exception $e) {
		//error_log($e->getMessage(), 1, "cjwalsh@ymail.com");
		Util::json($e->getMessage());
	}*/
	
	Util::json(true);
} else {
	Util::json(false);
}
?>