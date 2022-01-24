<?php
require_once '../../config.php';

set_include_path("." . PATH_SEPARATOR . APP_LIB_PATH . PATH_SEPARATOR . APP_PEAR_PATH . PATH_SEPARATOR . APP_ZEND_PATH);

require_once 'Autoloader.php';

//$log = new Ode_Log(APP_PATH . DIRECTORY_SEPARATOR . "log.db");

$dbo = new \Ode\DBO(APP_DB_HOST, APP_DB_NAME, APP_DB_USER, APP_DB_PASSWD);

$auth = new \Ode\Auth();

@session_start();

$manager = new \Ode\Manager();
$manager->setURI(str_replace("/", "", APP_REL_URL));

$controller = new \Ode\Controller();
$controller->setFileCreation(false);

$view = new \Ode\View(APP_VIEW_PATH);
$view->setFileCreation(false);
// pass controller's auth into template
$view->assign("auth", $controller->getAuth());
//$view->setAssetsURI(APP_ASSETS_URL);
$view->assign("assets_url", APP_ASSETS_URL);
