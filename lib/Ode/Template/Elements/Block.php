<?php
/**
 * DOM Document rendering for Template Blocks <ode:Block />
 * 
 * This class will produce the necessary XML to be passed back to
 * the template engine for generating the proper HTML to be included
 * where the Block element is placed.
 * 
 * @package Ode_Template_Elements
 * @name Block
 * @author cjwalsh
 * @copyright Christopher Walsh 2010
 *
 */
class Ode_Template_Elements_Block extends Ode_Template_Element {
	/**
	 * The DOM document to be used by the included XML file.
	 * 
	 * @var DOMDocument
	 * @access private
	 */
	private $_import;
	
	/**
	 * Full path to the templates directory
	 * 
	 * @var string
	 * @access private
	 */
	private $_templatePath;
	
	/**
	 * The filename (without full path) of the XML file to be included
	 * within the layout's template.
	 * 
	 * @var string
	 * @access private
	 */
	private $_templateFile;
	
	/**
	 * Default extension of template XML files.
	 * 
	 * @var string
	 * @access public
	 */
	const TEMPLATE_EXT = ".xml";
	
	/**
	 * @var string
	 * @access public
	 */
	const ATTRIBUTE_DISPLAY = "display";
	
	/**
	 * @var string
	 * @access public
	 */
	const ATTRIBUTE_NAME = "name";
	
	/**
	 * Constructor
	 * 
	 * @param DOMNode $node
	 * @param string $path
	 * @return void
	 * @access public
	 */
	public function __construct(DOMNode $node, $path) {
		$this->setNode($node);
		$this->setPath($path);
		$this->setFile();
		$this->setParent($node->parentNode);
		$this->setImport();
	}
	
	/**
	 * Sets the included template's filename.
	 * 
	 * @return void
	 * @access private
	 */
	private function setFile() {
		$this->_templateFile = $this->getPath() . $this->getNode()->getAttribute(self::ATTRIBUTE_NAME) . self::TEMPLATE_EXT;
	}
	
	/**
	 * Retrieves the included template's filename.
	 * 
	 * @return string
	 * @access private
	 */
	private function getFile() {
		return $this->_templateFile;
	}
	
	/**
	 * Sets the full path to the template files directory.
	 * 
	 * @param string $path
	 * @return void
	 * @access private
	 */
	private function setPath($path) {
		$this->_templatePath = $path;
	}
	
	/**
	 * Retrieves the full apth to the template files directory
	 * 
	 * @return string
	 * @access private
	 */
	private function getPath() {
		return $this->_templatePath;
	}
	
	/**
	 * Sets the included template's XML content
	 * as a DOM property for this object and replaces
	 * the parent container's content with valid HTML
	 * 
	 * @return void
	 * @access private
	 */
	private function setImport() {	
		$this->_import = new DOMDocument();
		
		if(file_exists($this->getFile())) {
			if($this->getNode()->getAttribute(self::ATTRIBUTE_DISPLAY) == "" || $this->getNode()->getAttribute(self::ATTRIBUTE_DISPLAY) == "true") {
				$this->getImport()->load($this->getFile());
				
				$xpath = new DOMXPath($this->getImport());
				$blockNodes = $xpath->query("//" . Ode_Template_Engine::NS_PREFIX . "Block");
				if($blockNodes->length > 0) {
					for($i=0; $i<$blockNodes->length; $i++) {
						new Ode_Template_Elements_Block($blockNodes->item($i), Ode_Template_Engine::getInstance()->getPath());
					}
				}
				
				$this->getParent()->replaceChild($this->getParent()->appendChild($this->getNode()->ownerDocument->importNode($this->getImport()->firstChild, true)), $this->getNode());
				
			}
		} else {
			$errorNode = $this->getNode()->ownerDocument->createElement("div", "The template block, " . $this->getFile() . " does not exist.");
			$errorNode->setAttribute("style", "background:#fc83c7; border:1px solid #990000; padding:5px; color:#990000;");
			$error = $this->getNode()->ownerDocument->appendChild($errorNode);
			
			$this->getParent()->replaceChild($error, $this->getNode());
		}
	}
	
	/**
	 * Retrieves the included template's DOM document
	 * 
	 * @return DOMDocument
	 * @access private
	 */
	private function getImport() {
		return $this->_import;
	}
	
	public function getDOM() {}
	
	public function setDOM() {}
}