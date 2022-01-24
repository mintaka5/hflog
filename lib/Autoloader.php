<?php
class Autoloader {
	public static function load($name) {
		$path = preg_replace("/[_\\\\]{1}/", DIRECTORY_SEPARATOR, $name) . '.php';
		
		$paths = explode(PATH_SEPARATOR, get_include_path());
		foreach ($paths as $p) {
			if(file_exists($p . DIRECTORY_SEPARATOR . $path)) {
				require_once $path;
			}
		}
	}
}

require_once 'PEAR.php';

spl_autoload_register("Autoloader::load");

require_once 'Zend/Loader/Autoloader.php';

Zend_Loader_AutoLoader::getInstance();

require_once 'PHPMailer/PHPMailerAutoload.php';

require_once 'Geo/GreatCircle.php';