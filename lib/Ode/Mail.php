<?php
namespace Ode;

use Ode\Mail\Address;

class Mail {
	private static $_instance;
	private $_recipients;
	private $_sender;
	private $_body;
	private $_subject;
	private $_html = false;
	private $_headers;
	
	const MIME_VERSION = "1.0";
	const CONTENT_TYPE_HTML = "text/html; charset=utf-8";
	const CONTENT_TYPE_TXT = "text/plain; charset=utf-8";
	const CRLF = "\r\n";

	public function __construct() {	
		self::$_instance = $this;
		
		$this->_recipients = new \ArrayObject();
		$this->_headers = new \ArrayObject();
	}
	
	public function addRecipient(Address $address) {
		$this->_recipients->append($address);
	}
	
	public function setSender(Address $address) {
		$this->_sender = $address;
	}
	
	public function setBody($body) {
		$this->_body = $body;
	}
	
	public function setSubject($subject) {
		$this->_subject = $subject;
	}
	
	public function getRecipients() {
		return $this->_recipients;
	}
	
	public function getSender() {
		return $this->_sender;
	}
	
	public function getBody() {
		return $this->_body;
	}
	
	public function getSubject() {
		return $this->_subject;
	}
	
	public function isHtml($bool = null) {
		if(!is_null($bool)) {
			$this->_html = $bool;
		}
		
		return $this->_html;
	}
	
	public static function getInstance() {
		return self::$_instance;
	}
	
	public function send() {
		$this->setHeaders();
		
		try {
			$sent = mail($this->getRecipientList(), $this->getSubject(), $this->getBody(), $this->getHeaders());

            if(!$sent) {
                error_log("Email failed to send", 0);

                return false;
            }
		} catch (\Exception $e) {
			error_log($e->getTraceAsString(), 0);
		}

        return true;
	}
	
	private function getRecipientList() {
		$recipients = $this->getRecipients();
		$recipAry = array();
		foreach($recipients as $recip) {
			$recipAry[] = $recip->getRFC();
		}
		
		return implode(",", $recipAry);
	}
	
	private function setHeaders() {
		$this->_headers->offsetSet("From", $this->getSender()->getRFC());
		$this->_headers->offsetSet("Reply-To", $this->getSender()->getRFC());
		
		/**
		 * removed because mail() automaitcally adds this header from the $to argument
		 */
		//$this->_headers->offsetSet("To", $this->getRecipientList());
		
		$this->_headers->offsetSet("Subject", $this->getSubject());
		
		$this->_headers->offsetSet("MIME-Version", self::MIME_VERSION);

		if($this->isHtml() != false) {
			$this->_headers->offsetSet("Content-Type", self::CONTENT_TYPE_HTML);
		} else {
			$this->_headers->offsetSet("Content-Type", self::CONTENT_TYPE_TXT);
		}
		
		$this->_headers->offsetSet("Content-Transfer-Encoding", "7bit");

        $this->_headers->offsetSet('X-Mailer', 'PHP/' . phpversion());
	}
	
	private function getHeaders() {
		$headers = $this->_headers->getArrayCopy();
		
		$str = "";
		foreach ($headers as $name => $val) {
			$str .= $name . ": " . $val . self::CRLF;
		}
		
		return $str;
	}
}