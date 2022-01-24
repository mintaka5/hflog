<?php
@define('COINBASE_API_KEY', 'eb74405912b7e1dcdd008a6a9cf4c908d11ec72fc865f4c78ec5a77a1de4bf9b');
@define('COINBASE_APP_CLINET_ID', 'd17aad223df36f875317da2779b8d9c34d6f366aa36c7b94ed33dab408447bbc');
@define('COINBASE_APP_CLIENT_SECRET', '472107cdf38ed7d46c72fd422e419a4333224d61eb77024e24e7df1904c60c12');
@define('COINBASE_APP_CALLBACK_URL', 'http://apps.qualsh.com/btc/coinbase/callback');

switch(\Ode\Manager::getInstance()->getMode()) {
	default:
		switch(\Ode\Manager::getInstance()->getTask()) {
			default:
				die();
				$http = new HTTP_Request2('https://blockchain.info/api/receive', HTTP_Request2::METHOD_GET);
				$http->setAdapter('curl');
				$http->getUrl()->setQueryVariable('method', 'create');
				$http->getUrl()->setQueryVariable('address', BTC_WALLET_ADDRESS);
				$http->getUrl()->setQueryVariable('callback', 'http://apps.qualsh.com/btc/callback?secret=' . BTC_SECRET);
				
				$send = $http->send();
				
				if($send) {
					Util::debug(json_decode($send->getBody()));
				}				

				break;
		}
		break;
	case 'callback':
		switch(\Ode\Manager::getInstance()->getTask()) {
			default:
				if($_GET['test'] == true) {
					return;
				}

				if($_GET['secret'] != BTC_SECRET) {
					return;
				}

				$invoice_id = strval($_GET['invoice_id']);
				$transaction_hash = strval($_GET['transaction_hash']);
				$input_transaction_hash = strval($_GET['input_transaction_hash']);
				$input_address = strval($_GET['input_address']);
				$satoshi = intval($_GET['value']);
				$btc = intval($satoshi / 100000000);
				$uuid = UUID::get();

				$sth = \Ode\DBO::getInstance()->prepare("
					INSERT INTO bitcoin (id, invoice_id, transaction_hash, btc_value) VALUES (:id, :invoice, :trans, :btc);
				");
				$sth->bindParam(':id', $uuid, PDO::PARAM_STR, 50);
				$sth->bindParam(':invoice', $invoice_id, PDO::PARAM_STR, 255);
				$sth->bindParam(':trans', $transaction_hash, PDO::PARAM_STR, 255);
				$sth->bindParam(':btc', $btc, PDO::PARAM_INT, 16);

				try {
					$sth->execute();
				} catch(Exception $e) {
					die($e->getTraceAsString());
				}

				echo "*ok*";
				break;
		}
		break;
	case 'coinbase':
		switch(\Ode\Manager::getInstance()->getTask()) {
			default:
                                
				break;
			case 'callback':
				
                                die();
				break;
		}
		break;
}
die();
?>
