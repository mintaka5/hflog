<?php
require_once 'lib/Util.php';

$_GET = Util::getClean($_GET);
$text = $_GET['text'];

$fontFile = './assets/arial.ttf';
$fontSize = 8;

$typeSpace = imagettfbbox($fontSize, 0, $fontFile, $text);

$imageWidth = abs($typeSpace[4] - $typeSpace[0]) + 10;
$imageHeight = abs($typeSpace[5] - $typeSpace[1]) + 10;

$im = imagecreatetruecolor($imageWidth+2, $imageHeight+2);

$blue = imagecolorallocate($im, 37, 92, 141);
$white = imagecolorallocate($im, 255, 255, 255);

imagefilledrectangle($im, 0, 0, $imageWidth+2, $imageHeight+2, $white);
imagefilledrectangle($im, 1, 1, $imageWidth, $imageHeight, $blue);

$x = 5;
$y = $imageHeight - 5;

imagettftext($im, $fontSize, 0, $x, $y, $white, $fontFile, $text);

header('Content-type: image/png');
imagepng($im);
imagedestroy($im);
exit();