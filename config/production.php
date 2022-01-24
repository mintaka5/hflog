<?php
@define('APP_PATH', '/home/cj5/webapps/app_hf');
@define('APP_LIB_PATH', APP_PATH . DIRECTORY_SEPARATOR . 'lib');
@define('APP_PEAR_PATH', '/home/cj5/lib/php/pear/pear/php');
@define('APP_ZEND_PATH', '/home/cj5/lib/php/zend/library');
@define('APP_CACHE_PATH', APP_PATH . DIRECTORY_SEPARATOR . 'views/cached');

/**
 * Live server settings
 */
@define('APP_DOMAIN', $_SERVER['SERVER_NAME']);
@define('APP_REL_URL', '/');
@define('APP_SITE_URL', 'https://' . APP_DOMAIN . APP_REL_URL);
@define('APP_ASSETS_URL', APP_REL_URL . '');
@define('APP_SITE_TITLE', 'HF Logbook');

@define('APP_SITE_CONTACT_EMAIL', 'cjwalsh@ymail.com');

/**
 * Live server settings
 */
@define('APP_DB_HOST', 'localhost');
@define('APP_DB_NAME', 'db_hf');
@define('APP_DB_PASSWD', 'M8Y8UQ0JsIogQY0y');
@define('APP_DB_USER', 'user_hf');

/**
 * SMTP settings
 */
@define('SMTP_HOST', 'smtp.webfaction.com');
@define('SMTP_PORT', 465);
@define('SMTP_USER', 'mailbox_hf');
@define('SMTP_PASSWORD', 'ehc121212');
@define('SMTP_EMAIL', 'info@hflog.us');
@define('SMTP_NAME', 'Chris Walsh');

/**
 * where the audio files are stored
 */
@define('AUDIO_STORAGE_PATH', '/home/cj5/data/hflogs');

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set('log_errors', 'On');
ini_set('display_errors', 'Off');
ini_set('html_errors', 'Off');
ini_set('error_log', '/home/cj5/data/logs/hf_php_errors.log');

@define('LOG_VIEW_QUOTA', 500);

@define('TOKENER_KEY', 'fHm9ml3DHIA');
