<?php
require_once 'config.php';

set_include_path("." . PATH_SEPARATOR . APP_LIB_PATH .
    PATH_SEPARATOR . APP_PEAR_PATH .
    PATH_SEPARATOR . APP_ZEND_PATH .
    PATH_SEPARATOR . APP_PATH);

require_once 'Autoloader.php';

$dbo = null;
try {
    $dbo = new Ode\DBO(APP_DB_HOST, APP_DB_NAME, APP_DB_USER, APP_DB_PASSWD, array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'
    ));
} catch (PDOException $e) {
    error_log($e->getTraceAsString(), 0);
} catch (Exception $e) {
    error_log($e->getTraceAsString(), 0);
}

/**
 * on the dev site, enable MySQL
 * error reporting
 */
if($_SERVER['SERVER_NAME'] === DEVELOPMENT_SERVER) {
    $dbo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

$auth = new SiteAuth();

@session_start();

/**
 * check to see if cookie is available
 * and if not, force log out of user
 */
$auth->checkCookie();

/**
 * check session time
 */
$auth->checkSessionTime();

$manager = new \Ode\Manager();
$manager->setURI(str_replace("ode/", "", APP_REL_URL));
$manager->setTitle(APP_SITE_TITLE);
$manager->setFullURL(APP_SITE_URL);

$controller = new \Ode\Controller();
$controller->setFileCreation(false);

$view = new \Ode\View(APP_VIEW_PATH);
$view->setFileCreation(false);
// pass controller's auth into template
$view->assign("auth", $controller->getAuth());
$view->assign("assets_url", APP_ASSETS_URL);

// include all site-wide control scripts (globals.php)
require_once 'globals.php';

// set & display the template layout
$view->setLayout("layout.tpl.php");

$view->display($view->getLayout());
