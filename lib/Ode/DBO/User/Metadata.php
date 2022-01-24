<?php
namespace Ode\DBO\User;

class Metadata extends \Metadata {
    const TABLE_NAME = 'user_meta';
    const MODEL_NAME = 'Ode\DBO\User\Metadata\Model';
    
    const META_PUBLIC_KEY = 'public_key';
    const META_PRIVATE_KEY = 'private_key';
    
    const META_VISITS_LOG_KEY = 'visits_log';
    const META_FIRST_VISIT_KEY = 'first_visit';
    const META_LAST_LOGIN = 'last_login';

    const META_LOCATION_NAME = 'location_name';
    
    public function __construct() {
        parent::__construct(self::TABLE_NAME);
    }
}

