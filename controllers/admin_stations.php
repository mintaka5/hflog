<?php
if(!\Ode\Auth::getInstance()->isAdmin()) {
    header('Location: ' . \Ode\Manager::getInstance()->friendlyAction('auth'));
    exit();
}

switch(\Ode\Manager::getInstance()->getMode()) {
    default:
        switch(\Ode\Manager::getInstance()->getTask()) {
            default:
                
                break;
        }
        break;
}