<?php
namespace Ode\Mail;

class Address {
	private $_email;
	private $_name;
	
	public function __construct($email = false, $name= false) {
		if($email != false) {
			$this->setEmail($email);
		}
		
		if($name != false) {
			$this->setName($name);
		}
	}
	
	public function setName($name) {
		$this->_name = $name;
	}
	
	public function setEmail($email) {
		$this->_email = $email;
	}
	
	public function getEmail() {
		return $this->_email;
	}
	
	public function getName() {
		return $this->_name;
	}
	
	public function getRFC() {
		if($this->getName() !== false) {
			return $this->getName() . " <" . $this->getEmail() . ">";
		} else {
			return $this->getEmail();
		}
	}
}