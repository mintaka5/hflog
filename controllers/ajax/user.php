<?php
require_once './init.php';

switch(\Ode\Manager::getInstance()->getMode()) {
    default:
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:

                break;
        }
        break;
    case 'location':
        /**
         * @var \Ode\DBO\User\Model
         */
        $user = \Ode\Auth::getInstance()->getSession();

        echo json_encode($user->coords());
        exit();
        break;
}