<?php
if (!\Ode\Auth::getInstance()->isAuth()) {
    header('HTTP/1.0 404 Not Found');
    exit(0);
}

switch (\Ode\Manager::getInstance()->getMode()) {
    default:
        /**
         * We are only going to acquire audio by log
         * @var \Ode\DBO\Hflog\Model
         */
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
        $log = \Ode\DBO\Hflog::getOneById($id);

        if (empty($log)) {
            header('HTTP/1.0 404 Not Found');
            exit(0);
        }

        $audioFile = $log->audio();
        /**
         * only get first file
         */
        $audioFile = $audioFile[0];
        $audioFile = AUDIO_STORAGE_PATH . $audioFile->filename;
        $audioPath = realpath($audioFile);
        break;
    case 'item':
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
        $audioFile = \Ode\DBO\SWAudio::getOneById($id);

        $audioFile = AUDIO_STORAGE_PATH . $audioFile->filename;
        $audioPath = realpath($audioFile);
        break;
}

header('Content-Type: ' . mime_content_type($audioPath));
header('Content-Length: ' . filesize($audioPath));

readfile($audioPath);

exit(0);