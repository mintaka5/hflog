<?php
/**
 * Object to manipulate template variables.
 * 
 * @author cjwalsh
 * @copyright Christopher Walsh 2010
 * @package Ode_Template
 * @name Variable
 *
 */
class Ode_Template_Variable {
	/**
	 * Variable name
	 * 
	 * @var string
	 * @access private
	 */
	private $_name;
	
	/**
	 * 
	 * @var mixed
	 * @access private
	 */
	private $_value;
	
	/**
	 * Constructor
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @access public
	 * @return void
	 */
	public function __construct($name = null, $value = null) {
		if(!is_null($name)) {
			$this->setName($name);
		}
		
		if(!is_null($value)) {
			$this->setValue($value);
		}
	}
	
	/**
	 * Sets variable value
	 * 
	 * @param mixed $value
	 * @access public
	 * @return void
	 */
	public function setValue($value) {
		$this->_value = $value;
	}
	
	/**
	 * Retrieve variable's value
	 * 
	 * @access public
	 * @return mixed
	 */
	public function getValue() {
		return $this->_value;
	}
	
	/**
	 * Sets variable name
	 * 
	 * @param string $name
	 * @access public
	 * @return void
	 */
	public function setName($name) {
		$this->_name = $name;
	}
	
	/**
	 * Retrieves variable's name
	 * 
	 * @return string
	 * @access public
	 */
	public function getName() {
		return $this->_name;
	}
}