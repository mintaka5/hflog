<?php
/**
 * Interface for XML template Element
 * 
 * @author cjwalsh
 * @copyright Christopher Walsh 2010
 * @package Ode_Template
 * @name IElement
 * 
 */
interface Ode_Template_IElement {
	/**
	 * 
	 * @access public
	 */
	public function getNode();
	
	/**
	 * 
	 * @param DOMNode $node
	 * @access public
	 */
	public function setNode(DOMNode $node);
	
	/**
	 * 
	 * @access public
	 */
	public function getParent();
	
	/**
	 * 
	 * @param DOMNode $node
	 * @access public
	 */
	public function setParent(DOMNode $node);
	
	public function setDOM();
	
	public function getDOM();
}