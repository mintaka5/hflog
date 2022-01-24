<?php
namespace Ode\Google;

class OAuth {
	const SESSION_VAR = "GOOGLE_OAUTH";
	const TOKEN_URL = "https://accounts.google.com/o/oauth2/token";
	const AUTH_URL = "https://accounts.google.com/o/oauth2/auth";
	
	private $clientId;
	private $clientSecret;
	private $redirectUri;
	private $accessToken = false;
	private $code = false;
	private $scope = false;
	
	/**
	 * 
	 * Constructor
	 * @param string $client_id Google API client ID
	 * @param string $secret Google API client secret
	 * @param string $redirect Google API client redirect URI
	 */
	public function __construct($client_id, $secret, $redirect) {
		$this->setId($client_id);
		$this->setSecret($secret);
		$this->setRedirect($redirect);
	}
	
	public function setAccessToken() {
		if(!isset($_SESSION[self::SESSION_VAR])) {
			if(isset($_REQUEST['code'])) {
				$req = new \HTTP_Request2(self::TOKEN_URL, HTTP_Request2::METHOD_POST);
				$req->setConfig("ssl_verify_peer", false);
				$req->addPostParameter("code", $_REQUEST['code']);
				$req->addPostParameter("client_id", $this->getId());
				$req->addPostParameter("client_secret", $this->getSecret());
				$req->addPostParameter("redirect_uri", $this->getRedirect());
				$req->addPostParameter("grant_type", "authorization_code");
				
				try {
					$res = $req->send();
					$jsonStr = $res->getBody();
					$json = new \Services_JSON();
						
					$_SESSION[self::SESSION_VAR] = $json->decode($jsonStr);
						
					header("Location: ".$this->getRedirect());
					exit();
				} catch(\Exception $e) {
					die($e->getTraceAsString());
				}
			}
			
			$req = new \HTTP_Request2(self::AUTH_URL, HTTP_Request2::METHOD_GET);
			$req->setConfig("ssl_verify_peer", false);
			$req->getUrl()->setQueryVariable("response_type", "code");
			$req->getUrl()->setQueryVariable("client_id", $this->getId());
			$req->getUrl()->setQueryVariable("redirect_uri", $this->getRedirect());
			$req->getUrl()->setQueryVariable("scope", $this->getScope());
			$req->getUrl()->setQueryVariable("state", "Ode_Google_OAuth");
			$req->getUrl()->setQueryVariable("access_type", "offline");
			$req->getUrl()->setQueryVariable("approval_prompt", "auto");
			
			try {
				$res = $req->send();
				header("Location:".$res->getHeader("location"));
				exit();
			} catch(\Exception $e) {
				//die($e->getTraceAsString());
			}
		}
	}
	
	public function getAccessToken() {
		return $_SESSION[self::SESSION_VAR]->access_token;
	}
	
	public function setScope($scope) {
		$this->scope = $scope;
	}
	
	public function getScope() {
		if($this->scope == false) {
			throw new \Exception("You must provide a Google API scope http://code.google.com/apis/accounts/docs/OAuth2Login.html#scopeparameter", E_ERROR);
		}
		
		return $this->scope;
	}
	
	public function setCode($code) {
		$this->code = $code;
	}
	
	public function getCode() {
		return $this->code;
	}
	
	public function setId($client_id) {
		$this->clientId = $client_id;
	}
	
	public function getId() {
		return $this->clientId;
	}
	
	public function setSecret($secret) {
		$this->clientSecret = $secret;
	}
	
	public function getSecret() {
		return $this->clientSecret;
	}
	
	public function setRedirect($uri) {
		$this->redirectUri = $uri;
	}
	
	public function getRedirect() {
		return $this->redirectUri;
	}
}
?>