<?php
require_once './init.php';

@define("DS", DIRECTORY_SEPARATOR);

$audioPath = AUDIO_STORAGE_PATH;
$fullAudioPath = $audioPath . DS . "audio" . DS . "stations" . DS;

$databasePath = "/audio/stations/" . date("Y") . "/" . date("m") . "/" . date("d") . "/";

$fileName = $_FILES['file']['name'];
$fileName = preg_replace("/[^\w\._]+/", "_", $fileName);

$databaseName = $databasePath . $fileName;

if (!file_exists($fullAudioPath . date("Y"))) {
    mkdir($fullAudioPath . date("Y"));
}

if (!file_exists($fullAudioPath . date("Y") . DS . date("m"))) {
    mkdir($fullAudioPath . date("Y") . DS . date("m"));
}

if (!file_exists($fullAudioPath . date("Y") . DS . date("m") . DS . date("d"))) {
    mkdir($fullAudioPath . date("Y") . DS . date("m") . DS . date("d"));
}

$targetName = $audioPath . $databasePath . $fileName;

try {
    $theMove = move_uploaded_file($_FILES['file']['tmp_name'], $targetName);
    $pathInfo = pathinfo($targetName);

    \Ode\DBO::getInstance()->beginTransaction();

    $sth = \Ode\DBO::getInstance()->prepare("INSERT INTO sw_audio (filename, title) VALUES (:file, :title)");
    $sth->bindParam(":file", $databaseName, PDO::PARAM_STR);
    $sth->bindParam(":title", str_replace("_", " ", $pathInfo['filename']), PDO::PARAM_STR, 255);

    $sth->execute();

    $new_id = \Ode\DBO::getInstance()->lastInsertId();

    unset($sth);

    $log = \Ode\DBO\Hflog::getOneById($_REQUEST['_id']);

    $sth = \Ode\DBO::getInstance()->prepare("INSERT INTO hflog_audio_cnx (log_id, audio_id) VALUES (:log, :audio)");
    $sth->bindParam(":log", $log->id, PDO::PARAM_STR, 50);
    $sth->bindParam(":audio", $new_id, PDO::PARAM_INT, 11);

    $sth->execute();

    unset($sth);

    if ($log->hasLocation()) {
        $station = $log->location()->location()->station();

        $sth = \Ode\DBO::getInstance()->prepare("INSERT INTO sw_station_audio_cnx (station_id, audio_id) VALUES (:station, :audio)");
        $sth->bindParam(":station", $station->id, PDO::PARAM_STR, 50);
        $sth->bindParam(":audio", $new_id, PDO::PARAM_INT, 11);

        $sth->execute();
    }

    \Ode\DBO::getInstance()->commit();

    \Ode\Utils\Json::encode(array_merge($_FILES, $_POST));
} catch (\Exception $e) {
    \Ode\Utils\Json::encode($e->getMessage());
}