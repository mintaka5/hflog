<?php
namespace Ode\DBO\Language;

class Model implements \JsonSerializable {
	public $iso;
	public $language;
	
	public function __construct() {}
	
	public function __toString() {
		if($this->language) {
			return $this->language;
		}
		
		return "";
	}

	/**
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	function jsonSerialize()
	{
		return [
			'iso' => $this->iso,
			'language' => iconv('UTF-8', 'UTF-8//IGNORE', $this->language) // gotta handle those stupid invalid utf8
		];
	}
}
