<?php
/**
 * A collection of template varaibles
 * 
 * Allows for the gathering and dissemination
 * of template-based variables, and adds them to 
 * a data stack for later retrieval
 * 
 * @author cjwalsh
 * @copyright Christopher Walsh 2010
 * @package Ode_Template_Variable
 * @name Collection
 *
 */
class Ode_Template_Variable_Collection extends ArrayObject {
	/**
	 * Variable data stack
	 * 
	 * @var ArrayObject
	 * @access private
	 */
	private $_data;
	
	/**
	 * Constructor
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->_data = new ArrayObject();
	}
	
	/**
	 * Add a template varaible to the collection.
	 * 
	 * @param mixed $id
	 * @param Ode_Template_Variable $object
	 * @access public
	 * @return void
	 */
	public function add($id, Ode_Template_Variable $object) {
		$this->_data->offsetSet($id, $object);
	}
	
	/**
	 * Retrieves a template variable from the collection
	 * 
	 * @param mixed $id
	 * @access public
	 * @return Ode_Template_Variable
	 */
	public function get($id) {
		return $this->_data->offsetGet($id);
	}
}