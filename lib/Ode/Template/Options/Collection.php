<?php
/**
 * Populates form checkboxes, radio buttons or select menus
 * with data.
 * 
 * <code>
 * <?php
 * $template = new Ode_Template_Engine("/path/to/templates/folder/");
 * // set a options data source!
 * $template->set("selectDS", new Ode_Template_Options_Collection(
 * 	new Ode_Template_Option("test1", "Test 1"),
 * 	new Ode_Template_Option("test2", "Test 2"),
 * 	new Ode_Template_Option("test3", "Test 3")
 * ));
 * ?>
 * </code>
 * 
 * @author cjwalsh
 * @copyright Christopher Walsh 2010
 * @package Ode_Template_Options
 * @name Collection
 * @todo Provide an XML export of this data
 */
class Ode_Template_Options_Collection extends ArrayObject {
	/**
	 * Constructor
	 * 
	 * @return void
	 * @access public
	 */
	public function __construct() {
		$options = func_get_args();
		
		foreach($options as $option) {
			if($option instanceof Ode_Template_Option) {
				$this->add($option->getValue(), $option);
			}
		}
	}
	
	/**
	 * Adds an option to the collection
	 * 
	 * @param mixed $id
	 * @param Ode_Template_Option $object
	 * @return void
	 * @access public
	 */
	public function add($id, Ode_Template_Option $object) {
		$this->offsetSet($id, $object);
	}
	
	/**
	 * Retrieves a specified option from the collection
	 * 
	 * @param mixed $id
	 * @return Ode_Template_Option
	 * @access public
	 */
	public function get($id) {
		return $this->offsetGet($id);
	}
}