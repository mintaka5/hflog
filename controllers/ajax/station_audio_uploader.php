<?php
require './init.php';

$stationId = $_REQUEST['_id'];
$station = \Ode\DBO\SWStation::getOneById($stationId);

$newAudio = uploadAudioItem(
    $_FILES['file'],
    AUDIO_STORAGE_PATH,
    '/audio/stations/',
    DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array(
        'audio', 'stations', date('Y'), date('m'), date('d')
    )) . DIRECTORY_SEPARATOR
);

if($newAudio !== false) {
    // create station-to-audio relationship
    $sth = \Ode\DBO::getInstance()->prepare("
        INSERT INTO " . \Ode\DBO\SWStation\Audio\Cnx::TABLE_NAME . " (station_id, audio_id)
        VALUES (:stationid, :audioid)
    ");
    $sth->bindParam(":stationid", $station->id, PDO::PARAM_STR, 50);
    $sth->bindParam(":audioid", $newAudio, PDO::PARAM_INT, 11);

    try {
        $sth->execute();
    } catch (\Exception $e) {
        error_log($e->getMessage(), 0);
        \Ode\Utils\Json::encode($e->getMessage());
    }
}

\Ode\Utils\Json::encode(array_merge($_FILES, $_POST));

/**
 * @param array $item $_FILE instance
 * @return boolean|integer new audio instance's ID
 */
function uploadAudioItem($item, $basePath, $fullSuffix, $dbPath)
{
    $serverPath = $basePath . $fullSuffix;

    $fileName = $item['name'];
    $fileName = preg_replace('/[^\w\._]+/', '_', $fileName);

    $dbFilename = $dbPath . $fileName;

    if (!file_exists($serverPath . date('Y'))) {
        mkdir($serverPath . date('Y'));
    }

    $ymPath = array(date('Y'), date('m'));
    $ymDir = $serverPath . implode(DIRECTORY_SEPARATOR, $ymPath);
    if (!file_exists($ymDir)) {
        mkdir($ymDir);
    }

    $ymdPath = array(date('Y'), date('m'), date('d'));
    $ymdDir = $serverPath . implode(DIRECTORY_SEPARATOR, $ymdPath);
    if (!file_exists($ymdDir)) {
        mkdir($ymdDir);
    }

    $targetName = $basePath . $dbPath . $fileName;

    try {
        $theMove = move_uploaded_file($item['tmp_name'], $targetName);
        $pathInfo = pathinfo($targetName);

        \Ode\DBO::getInstance()->beginTransaction();

        $sth = \Ode\DBO::getInstance()->prepare("INSERT INTO sw_audio (filename, title) VALUES (:file, :title)");
        $sth->bindParam(":file", $dbFilename, PDO::PARAM_STR);
        $sth->bindParam(":title", str_replace('_', ' ', $pathInfo['filename']), PDO::PARAM_STR, 255);

        $sth->execute();

        $newId = \Ode\DBO::getInstance()->lastInsertId();

        \Ode\DBO::getInstance()->commit();

        return $newId;
    } catch (\Exception $e) {
        error_log($e->getMessage(), 0);
    }

    return false;
}