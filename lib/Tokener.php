<?php
use Base32\Base32;

class Tokener {
	private $key;
	private $hash;
	private $token;
	private $hashObj;
	
	public function __construct($key) {
		$this->key = $key;
		
		$this->setHash();
	}
	
	private function setHash() {
		$this->hashObj = new PasswordHash(8, false);
		$this->hash = $this->hashObj->HashPassword($this->key);
	}
	
	public function getToken() {
		return Base32::encode($this->hash);
	}
	
	public function isValid($token) {
		$non32 = Base32::decode($token);
		
		if($this->hashObj->CheckPassword($this->key, $non32)) {
			return true;
		}
		
		return false;
	}
}