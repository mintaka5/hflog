<?php
class Flickr_Rest {
	const BASE_API_URL = "http://api.flickr.com/services/rest/";
	const REQUEST_TOKEN_URL = 'http://www.flickr.com/services/oauth/request_token';
	const ACCESS_TOKEN_URL = 'http://www.flickr.com/services/oauth/access_token';
	const AUTHORIZE_URL = 'http://www.flickr.com/services/oauth/authorize';
	const AUTH_URL = 'http://www.flickr.com/services/oauth';
	
	const METHOD_PHOTOS_SEARCH = "flickr.photos.search";
	
	const PHOTO_EXTRAS = "description, license, date_upload, date_taken, owner_name, icon_server, original_format, last_update, geo, tags, machine_tags, o_dims, views, media, path_alias, url_sq, url_t, url_s, url_q, url_m, url_n, url_z, url_c, url_l, url_o";
	
	const TAG_MODE_ANY = "any";
	
	private $api_key = null;
	private $nsid = null;
	private $app_secret = null;
	private $access_token = false;
	private $redirect = false;
	
	public function __construct($api_key = false, $nsid = false, $app_secret = false, $redirect = false) {
		if($api_key != false) {
			$this->api_key = $api_key;
		}
		
		if($nsid != false) {
			$this->nsid = $nsid;
		}
		
		if($app_secret != false) {
			$this->app_secret = $app_secret;
		}
		
		if($redirect != false) {
			$this->redirect = $redirect;
		}
	}
	
	public function setAccessToken(Zend_Oauth_Token_Access $access_token) {
		$this->access_token = $access_token;
	}
	
	public function getAccessToken() {
		return $this->access_token;
	}
	
	public function getPhotoset($photoset_id, $format = "json") {
		$client = $this->getAccessToken()->getHttpClient(array(
				'callbackUrl' => $this->redirect,
				'siteUrl' => self::AUTH_URL,
				'consumerKey' => $this->api_key,
				'consumerSecret' => $this->app_secret,
				'requestTokenUrl' => self::REQUEST_TOKEN_URL,
				'accessTokenUrl' => self::ACCESS_TOKEN_URL,
				'authorizeUrl' => self::AUTHORIZE_URL
		));
		
		$adapter = new Zend_Http_Client_Adapter_Curl();
		$client->setAdapter($adapter);
		
		$client->setUri(self::BASE_API_URL);
		$client->setMethod(Zend_Http_Client::GET);
		$client->setParameterGet('method', 'flickr.photosets.getPhotos');
		//$client->setParameterGet('user_id', "me");
		$client->setParameterGet('api_key', $this->api_key);
		$client->setParameterGet('format', $format);
		//$client->setParameterGet('content_type', 1);
		$client->setParameterGet('photoset_id', $photoset_id);
		$client->setParameterGet('extras', self::PHOTO_EXTRAS);
		if($format == 'json') {
			$client->setParameterGet('nojsoncallback', 1);
		}
		
		$response = $client->request();
		return $response->getBody();
	}
	
	public function search($search_terms, $format = "json") {		
		$client = $this->getAccessToken()->getHttpClient(array(
			'callbackUrl' => $this->redirect,
			'siteUrl' => self::AUTH_URL,
			'consumerKey' => $this->api_key,
			'consumerSecret' => $this->app_secret,
			'requestTokenUrl' => self::REQUEST_TOKEN_URL,
			'accessTokenUrl' => self::ACCESS_TOKEN_URL,
			'authorizeUrl' => self::AUTHORIZE_URL
		));
		
		$adapter = new Zend_Http_Client_Adapter_Curl();
		$client->setAdapter($adapter);
		
		$client->setUri(self::BASE_API_URL);
		$client->setMethod(Zend_Http_Client::GET);
		$client->setParameterGet('method', 'flickr.photos.search');
		$client->setParameterGet('user_id', "me");
		$client->setParameterGet('api_key', $this->api_key);
		$client->setParameterGet('format', $format);
		$client->setParameterGet('content_type', 1);
		$client->setParameterGet('extras', self::PHOTO_EXTRAS);
		
		$tags = preg_replace("/[\W]+/", " ", $search_terms);
		$tags = preg_split("/[\s\t\r\n]+/i", $tags);
		$tags = implode(",", $tags);
		$client->setParameterGet('tags', $tags);
		
		$client->setParameterGet('tag_mode', 'any');
		$client->setParameterGet('text', $search_terms);
		if($format == 'json') {
			$client->setParameterGet('nojsoncallback', 1);
		}
		
		$response = $client->request();
		return $response->getBody();
	}
	
	public function getAll($page = 1, $per_page = 100, $format = "json") {
		$client = $this->getAccessToken()->getHttpClient(array(
		 	'callbackUrl' => $this->redirect,
			'siteUrl' => self::AUTH_URL,
			'consumerKey' => $this->api_key,
			'consumerSecret' => $this->app_secret,
			'requestTokenUrl' => self::REQUEST_TOKEN_URL,
			'accessTokenUrl' => self::ACCESS_TOKEN_URL,
			'authorizeUrl' => self::AUTHORIZE_URL
		));
		
		$adapter = new Zend_Http_Client_Adapter_Curl();
		$client->setAdapter($adapter);
		
		$client->setUri(self::BASE_API_URL);
		$client->setMethod(Zend_Http_Client::GET);
		$client->setParameterGet('method', 'flickr.photos.search');
		$client->setParameterGet('user_id', "me");
		$client->setParameterGet('api_key', $this->api_key);
		$client->setParameterGet('format', $format);
		$client->setParameterGet('content_type', 1);
		$client->setParameterGet('extras', self::PHOTO_EXTRAS);
		$client->setParameterGet('page', $page);
		$client->setParameterGet('per_page', $per_page);
		if($format == 'json') {
			$client->setParameterGet('nojsoncallback', 1);
		}
		
		$response = $client->request();
		return $response->getBody();
	}
}