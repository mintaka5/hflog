<?php
require_once './init.php';

$jsonAry = array();
$jsonAry['project'] = "Qualsh Homepage Rotatin Modules";
$jsonAry['modules'] = array();

/* $jsonAry['modules'][] = array(
	'id' => "12345",
	'image' => "",
	'thumbnail' => "",
	'category' => "test",
	'title' => "Test Title",
	'copy' => "There's no copy yet!",
	'link' => "#"
);

$jsonAry['modules'][] = array(
	'id' => "123245",
	'image' => "",
	'thumbnail' => "",
	'category' => "test",
	'title' => "Test Title 2",
	'copy' => "There's no copy yet!",
	'link' => "#"
); */

Util::json($jsonAry);
exit();
?>