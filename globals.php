<?php
require_once 'Base32.php';
require_once 'PasswordHash.php';

$tokener = new Tokener(TOKENER_KEY);
\Ode\View::getInstance()->assign('searchtoken', $tokener->getToken());

function getQuotaTimeLeft() {
    if(\Ode\Auth::getInstance()->isAuth() && (!\Ode\Auth::getInstance()->isPro() || !\Ode\Auth::getInstance()->isAdmin())) {
        $now = new \DateTime('now');
        $first_meta = intval(\Ode\DBO\User::getMeta()->getValue(\Ode\Auth::getInstance()->getSession()->id, \Ode\DBO\User\Metadata::META_FIRST_VISIT_KEY));
        $first = new \DateTime();
        $first->setTimestamp($first_meta);
        $first->modify('+1 hour');
        $diff = $first->diff($now);
        
        if($diff->h <= 1) {
            return $diff->format('%I mins. left');
        }
    }
    
    return false;
}

function getQuotaVisits($limit = 10) {
    if(\Ode\Auth::getInstance()->isAuth() && (!\Ode\Auth::getInstance()->isPro() || !\Ode\Auth::getInstance()->isAdmin())) {
        $time_left = getQuotaTimeLeft();
        if($time_left == false) {
            $visits = intval(\Ode\DBO\User::getMeta()->getValue(\Ode\Auth::getInstance()->getSession()->id, \Ode\DBO\User\Metadata::META_VISITS_LOG_KEY));
            if($visits <= $limit) {
                return strval(($limit-$visits)) . ' remaining';
            }
        }
    }
    
    return false;
}

/**
 * get bitcoin proxy address for payments to donations
 */
function getBitcoinProxy() {
	$invoice_id = UUID::get();

	$http = new \HTTP_Request2('https://blockchain.info/api/receive', \HTTP_Request2::METHOD_GET);
        $http->setAdapter('curl');
        $http->getUrl()->setQueryVariable('method', 'create');
        $http->getUrl()->setQueryVariable('address', BTC_WALLET_ADDRESS);
        $http->getUrl()->setQueryVariable('callback', 'http://apps.qualsh.com/btc/callback?secret=' . BTC_SECRET . '&invoice_id=' . $invoice_id);
	$send = $http->send();
	
	echo $http->getUrl()->getQueryVariable('callback');
	
	if($send) {
		$data = json_decode($send->getBody());
		return $data->input_address;
	}

	return false;
}
