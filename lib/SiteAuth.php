<?php
use Ode\Auth;
use Ode\DBO\User;
use Ode\DBO\User\Metadata;

class SiteAuth extends Auth {
	const USER_TYPE_ADMIN = "admin";
        const USER_TYPE_PRO = "pro";
        const USER_TYPE_REGISTERED = "reg";
        
        private $is_logged_in = false;
        
        private static $instance;
	
	public function __construct() {
            parent::__construct();

            $session = $this->hasCookie();
            if($session == true) {
                $this->is_logged_in = true;
            } else {
                $this->is_logged_in = false;
            }
            
            self::$instance = $this;
	}
        
        public static function getInstance() {
            return self::$instance;
        }
        
        public function isLoggedIn() {
            $session = $this->hasCookie();
            if($session == true) {
                return true;
            }
            
            return false;
        }
	
	public function isAdmin() {
            if($this->getSession()) {
                if($this->getSession()->type()->type_name == self::USER_TYPE_ADMIN) {
                    return true;
                }
            }

            return false;
	}
        
        public function isRegistered() {
            if($this->getSession()) {
                if($this->getSession()->type()->type_name == self::USER_TYPE_REGISTERED ||
                        $this->getSession()->type()->type_name == self::USER_TYPE_PRO ||
                        $this->getSession()->type()->type_name == self::USER_TYPE_ADMIN) {
                    return true;
                }
            }
            
            return false;
        }
        
        public function isPro() {
            if($this->getSession()) {
                if($this->getSession()->type()->type_name == self::USER_TYPE_PRO ||
                        $this->getSession()->type()->type_name == self::USER_TYPE_ADMIN) {
                    return true;
                }
            }
            
            return false;
        }
        
        public function hasMetQuota($quota = 10, $time_span = 3600) {
            if(!$this->isAuth()) { // no way, get registered, bitch
                return true;
            }
            
            if(!$this->isAdmin() || !$this->isPro()) {
                $first = User::getMeta()->getValue($this->getSession()->id, Metadata::META_FIRST_VISIT_KEY);
                
                $visits = User::getMeta()->getValue($this->getSession()->id, Metadata::META_VISITS_LOG_KEY);
                
                $now = time();
                $init_time = intval($first);
                $time_diff = $now - $init_time;
                if($time_diff <= $time_span && $visits >= $quota) { // if we are under an hour, and met quota
                    return true;
                }
                
                if($time_diff > $time_span) { // reset init time and logged visits for user
                    User::getMeta()->add($this->getSession()->id, Metadata::META_FIRST_VISIT_KEY, time(), true);
                    User::getMeta()->add($this->getSession()->id, Metadata::META_VISITS_LOG_KEY, 0, true);
                }
                
                $this->attendance();
            }
            
            return false;
        }
        
        public function attendance() {
            if($this->isLoggedIn()) {
                $init_time = User::getMeta()->getValue($this->getSession()->id, Metadata::META_FIRST_VISIT_KEY);
                $visits = User::getMeta()->getValue($this->getSession()->id, Metadata::META_VISITS_LOG_KEY);

                if(empty($init_time)) {
                    User::getMeta()->add($this->getSession()->id, Metadata::META_FIRST_VISIT_KEY, time(), true);
                    $init_time = User::getMeta()->getValue($this->getSession()->id, Metadata::META_FIRST_VISIT_KEY);
                }

                if(empty($visits)) {
                    User::getMeta()->add($this->getSession()->id, Metadata::META_VISITS_LOG_KEY, 0, true);
                    $visits = User::getMeta()->getValue($this->getSession()->id, Metadata::META_VISITS_LOG_KEY);
                }
            }
            
            $new_visits = intval($visits)+1;
            User::getMeta()->add($this->getSession()->id, Metadata::META_VISITS_LOG_KEY, $new_visits, true);
        }
}