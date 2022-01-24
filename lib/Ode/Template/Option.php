<?php
/**
 * An option element to be used within form select menus, check boxes or radio buttons
 * 
 * @author cjwalsh
 * @copyright Christopher Walsh 2010
 * @package Ode_Template
 * @name Option
 *
 */
class Ode_Template_Option {
	/**
	 * 
	 * @var string
	 * @access private
	 */
	private $_title;
	
	/**
	 * 
	 * @var string
	 * @access private
	 */
	private $_value;
	
	/**
	 * Constructor
	 * 
	 * @param string $title
	 * @param string $value
	 * @return void
	 * @access public
	 */
	public function __construct($title = null, $value = null) {
		if(!is_null($title)) {
			$this->setTitle($title);
		}
		
		if(!is_null($value)) {
			$this->setValue($value);
		}
	}
	
	/**
	 * Sets option label/title (whatever is visual text)
	 * 
	 * @param string $title
	 * @access public
	 */
	public function setTitle($title) {
		$this->_title = $title;
	}
	
	/**
	 * Sets the option's machine-readable value
	 * 
	 * @param string $value
	 * @access public
	 */
	public function setValue($value) {
		$this->_value = $value;
	}
	
	/**
	 * Retrieves th option's title/label
	 * 
	 * @return string
	 * @access public
	 */
	public function getTitle() {
		return $this->_title;
	}
	
	/**
	 * Retrieves the option's value
	 * 
	 * @return string
	 * @access public
	 */
	public function getValue() {
		return $this->_value;
	}
}