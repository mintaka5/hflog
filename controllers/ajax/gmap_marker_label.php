<?php
ini_set("display_errors", "Off");
error_reporting(E_ALL);

require_once './init.php';

//putenv("GDFONTPATH=" . realpath(APP_PATH . "/assets"));
//Util::debug($_GET['txt']); die();

@define("CHAR_WIDTH", 7);

switch(\Ode\Manager::getInstance()->getMode()) {
	default:
		$txt = isset($_GET['txt']) ? trim($_GET['txt']) : "";
		
		$txtW = (CHAR_WIDTH * strlen($txt)) + 5;
	
		$image = imagecreate($txtW, 16);
		$blueBG = imagecolorallocate($image, 0, 0, 255);
		$whiteLineColor = imagecolorallocate($image, 255, 255, 255);
		
		$imgW = imagesx($image)-1;
		$imgH = imagesy($image)-1;
		
		imageline($image, 0, 0, $imgW, 0, $whiteLineColor);
		imageline($image, $imgW, 0, $imgW, $imgH, $whiteLineColor);
		imageline($image, $imgW, $imgH, 0, $imgH, $whiteLineColor);
		imageline($image, 0, $imgH, 0, 0, $whiteLineColor);
		
		//imagettftext($image, 10, 0, 0, 0, $whiteLineColor, "arial.ttf", $txt);
		imagestring($image, 2, 6, 1, $txt, $whiteLineColor);
		
		header("Content-Type: image/png");
		imagepng($image);
		break;
}
exit();
?>