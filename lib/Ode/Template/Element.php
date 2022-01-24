<?php
/**
 * Base class for template elements
 * 
 * Provides a base object for reusable features
 * of XML template elements.
 * 
 * @author cjwalsh
 * @copyright Christopher Walsh 2010
 * @package Ode_Template
 * @name Element
 * @abstract
 * 
 */
abstract class Ode_Template_Element implements Ode_Template_IElement {
	/**
	 * Base node for each template element
	 * 
	 * @var DOMNode
	 * @access private
	 */
	private $_node;
	
	/**
	 * The container node that encloses
	 * the base node
	 * 
	 * @var DOMNode
	 * @access private
	 */
	private $_parent;
	
	private $_DOM;
	
	/**
	 * Sets the base node
	 * 
	 * @param DOMNode $node
	 * @access public
	 * @return void
	 */
	public function setNode(DOMNode $node) {
		$this->_node = $node;
	}
	
	/**
	 * Retrieves the base node
	 * 
	 * @access public
	 * @return DOMNode
	 */
	public function getNode() {
		return $this->_node;
	}
	
	/**
	 * Sets the container node
	 * 
	 * @param DOMNode $node
	 * @access public
	 * @return void
	 */
	public function setParent(DOMNode $node) {
		$this->_parent = $node;
	}
	
	/**
	 * Retrieves the container node
	 * 
	 * @access public
	 * @return DOMNode
	 */
	public function getParent() {
		return $this->_parent;
	}
}