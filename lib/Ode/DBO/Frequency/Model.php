<?php
namespace Ode\DBO\Frequency;

class Model {
	public $id;
	public $frequency;
	public $tag;
	public $ctcss_tone_id;
	public $dcs_tone_id;
	public $nac;
	public $is_encrypted;
	public $mode_id;
	public $description;
	public $is_active;
	public $user_id;
	public $created;
	public $modified;
	
	public function __construct() {
		$this->description = stripslashes($this->description);
		$this->description = strip_tags($this->description);
	}
	
	public function tone($default = "&nbsp;") {
		if(!empty($this->ctcss_tone_id)) {
			$result = Ode_DBO_CTCSS::getOneById($this->ctcss_tone_id);
			$tone = (string)$result->hertz . " PL";
		} elseif(!empty($this->dcs_tone_id)) {
			$result = Ode_DBO_DCS::getOneById($this->dcs_tone_id);
			$tone = (string)$result->dcs . " DPL";
		} elseif(!empty($this->nac)) {
			$tone = (string)$this->nac . " NAC";
		} else {
			$tone = $default;
		}
		
		return $tone;
	}
	
	public function county() {
		return \Ode\DBO::getInstance()->query("
			SELECT county.*
			FROM frequency_county_cnx AS cnx
			LEFT JOIN counties AS county ON (county.cid = cnx.county_id)
			WHERE cnx.frequency_id = " . \Ode\DBO::getInstance()->quote($this->id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject("Ode_DBO_County_Model");
	}
	
	public function user() {
		return \Ode\DBO\User::getOneById($this->user_id);
	}
	
	public function mode() {
		return Ode_DBO_Frequency_Mode::getOneById($this->mode_id);	
	}
	
	public function description($default = "No description") {
		if(empty($this->description)) {
			return $default;
		}
		
		return $this->description;
	}
	
	public function group() {
		return Ode_DBO_Group::getOneByFrequency($this->id);
	}
}