<?php
require_once './init.php';

if(!\Ode\Auth::getInstance()->isAuth()) {
    Util::json(false, 'Unauthorized access');
    exit();
}

switch(\Ode\Manager::getInstance()->getMode()) {
    default:

        break;
    case 'get':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:

                break;
            case 'all':
                $languages = \Ode\DBO\Language::getAll();

                \Ode\Utils\Json::encode($languages);
                break;
        }
        break;
}