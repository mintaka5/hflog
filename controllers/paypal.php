<?php
switch (\Ode\Manager::getInstance()->getMode()) {
    case 'thanks':
        
        
        \Ode\View::getInstance()->assign('transaction_id', $_REQUEST['tx']);
        break;
    default:
        break;
    case 'check':
        require_once 'PayPal/vendor/autoload.php';
        
        try {
            $api = new PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential(PAYPAL_CLIENT_ID, PAYPAL_SECRET));
            /*$api->setConfig(array(
                'mode' => 'sandbox'
            ));*/
            
            
        } catch(Exception $e) {
            Util::debug($e->getMessage());
            exit(0);
        }
        
        try {
            $sale = \PayPal\Api\Sale::get('9GY68171D1802832X', $api);
            echo "HELLO";
            Util::debug(json_decode($sale));
        } catch(PayPal\Exception\PPConnectionException $e) {
            Util::debug($e->getMessage());
        }
        
        break;
}
?>