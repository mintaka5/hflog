<?php
@define('DEVELOPMENT_SERVER', 'dev.cj5.webfactional.com');
@define('PRODUCTION_SERVER', 'hflog.us');

switch($_SERVER['SERVER_NAME']) {
    default:
    case DEVELOPMENT_SERVER:
        require_once 'config/development.php';
        break;
    case PRODUCTION_SERVER:
        require_once 'config/production.php';
        break;
}

@define("APP_CONTROLLER_PATH", APP_PATH . DIRECTORY_SEPARATOR . "controllers");
@define("APP_VIEW_PATH", APP_PATH . DIRECTORY_SEPARATOR . "views");

@define('DEFAULT_USER_AGENT', 'Mozilla/5.0 (Windows; U; Windows NT 6.0; ca; rv:1.9.1.1) Gecko/20090715 Firefox/3.5.1');

// date form entry RegEx string
@define("DATETIME_FORM_REGEX", "/\d{2}\/\d{2}\/\d{4}\s\d{1,2}\:\d{2}\s(am|pm)/");

// Flickr API
@define("FLICKR_REST_PREFIX", "http://api.flickr.com/services/rest/");
@define("FLICKR_API_KEY", "");

@define("QUICKFORM2_REGEX_EMAIL", "/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/");

@define("FLICKR_BASE_API_URL", "http://api.flickr.com/services/rest/");
@define("FLICKR_API_KEY", "");
@define("FLICKR_NSID", "");
@define("FLICKR_APP_SECRET", "");

@define('BTC_SECRET', 'Tj1oqBG0KGjG4HGZicoeA');
@define('BTC_WALLET_ADDRESS', '1N2Lr6naW7oVy1UcnVRkqJZA33fvNLcgo3');

@define('PAYPAL_SANDBOX_CLIENT_ID', 'Afy-5hCWuTFPtYe84EDrzXAEI0nakwIOZ5GgACpR_vGxd2wj_myfh8Nq9Hf0');
@define('PAYPAL_SANDBOX_SECRET', 'EIn86xDDgbNdKK3SxwqkFBpGsjFymBpGEEfRMlWRfARnHuV54XVbPqqnijeZ');

@define('PAYPAL_CLIENT_ID', 'AbqzPxDJ-lyoVcru5rgwoe-_n21hVoAsV5acO2k1GMvstUN-8F_w8j-X_j40');
@define('PAYPAL_SECRET', 'EIdqnhCPhavFnQ4r9WVjf9hMrk3vz6KyVGdmWgAlRth5If6Q68zu581aQkOK');

//@todo REMOVE ME LATER (providing push for git deploy)