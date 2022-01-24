<?php
/**
 * Main object responsible for generating HTML template
 * 
 * Generates valid HTML from an XML-based template layout
 * 
 * <code>
 * <?php
 * $template = new Ode_Template_Engine("/path/to/template/directory/");
 * // ... do all your template assignments here ...
 * $template->format();
 * ?>
 * </code>
 * 
 * @author cjwalsh
 * @copyright Christopher Walsh 2010
 * @package Ode_Template
 * @name Engine
 * @todo Provide a method for including templates via PHP i.e. $template->include("filename.xml");
 * 
 */
class Ode_Template_Engine {
	/**
	 * 
	 * @var Ode_Template_Engine
	 * @access private
	 */
	private static $_instance;
	
	/**
	 * 
	 * @var DOMDocument
	 * @access private
	 */
	private $_doc = false;
	
	/**
	 * Path to template directory
	 * 
	 * @var string
	 * @access private
	 */
	private $_path = false;
	
	/**
	 * Default layout template filename
	 * 
	 * @var string
	 * @access private
	 */
	private $_layout = "layout.xml";
	
	/**
	 * 
	 * @var DOMXPath
	 * @access private
	 */
	private $_xpath = false;
	
	/**
	 * The template engine's user-defined variables to be used
	 * dynamically within the template
	 * 
	 * @var Ode_Template_Variable_Colection
	 * @access private
	 */
	private $_variables = false;
	
	/**
	 * Default template extension
	 * 
	 * @var string
	 * @access public
	 */
	const DEFAULT_EXT = ".xml";
	
	/**
	 * Namespace prefix
	 * 
	 * @var string
	 * @access public
	 */
	const NS_PREFIX = "ode:";
	
	/**
	 * Namespace URI
	 * 
	 * @var string
	 */
	const NS_URI = "http://www.odeweb.com";
	
	/**
	 * Constructor
	 * 
	 * @param string $path
	 * @access public
	 * @return void
	 */
	public function __construct($path = null) {
		if(!is_null($path)) {
			$this->setPath($path);
		}
		
		$this->_variables = new Ode_Template_Variable_Collection();
		
		$this->setDocument();
		
		$this->setXPath();
		
		self::$_instance = $this;
	}
	
	/**
	 * Retrieves an instance of Ode_Template_Engine
	 * 
	 * @access public
	 * @return Ode_Template_Engine
	 */
	public static function getInstance() {
		return self::$_instance;
	}
	
	/**
	 * Sets the main layout template's filename
	 * 
	 * @param string $layout
	 * @access private
	 * @return void
	 */
	private function setLayout($layout) {
		$this->_layout = $layout;
	}
	
	/**
	 * Build all XML elements, and output
	 * corresponding HTML content.
	 * 
	 * @access public
	 * @return boolean
	 */
	public function format() {
		/**
		 * Gather and reformat all template blocks
		 * 
		 * @var DOMNodeList
		 */
		$blockNodes = $this->getXPath()->query("//" . self::NS_PREFIX . "Block");
		foreach($blockNodes as $blockNode) {
			/**
			 * Block HTML output
			 * 
			 * @var Ode_Template_Elements_Block
			 */
			$block = new Ode_Template_Elements_Block($blockNode, $this->getPath());
		}
		
		/**
		 * Gather and reformat all template forms
		 * 
		 * @var DOMNodeList
		 */
		$formNodes = $this->getXPath()->query("//" . self::NS_PREFIX . "Form");
		foreach($formNodes as $formNode) {
			/**
			 * Form HTML output
			 * 
			 * @var Ode_Template_Elements_Form
			 */
			$form = new Ode_Template_Elements_Form($formNode);
		}
		
		/**
		 * Gather all data grids from template
		 * 
		 * @var DOMNodeList
		 */
		$dataGridNodes = $this->getXPath()->query("//" . self::NS_PREFIX . "DataGrid");
		foreach($dataGridNodes as $dataGridNode) {
			/**
			 * 
			 * @var Ode_Template_Elements_DataGrid
			 */
			$dataGrid = new Ode_Template_Elements_DataGrid($dataGridNode);
		}
		
		header("Content-Type: text/html");
		echo $this->getDocument()->saveHTML();
		
		return true;
	}
	
	/**
	 * Retrives the primary template layout filename
	 * 
	 * @access private
	 * @return string
	 */
	private function getLayout() {
		return $this->_layout;
	}
	
	/**
	 * Sets the objects XPath object for querying 
	 * tag elements in XML template
	 * 
	 * @access private
	 * @return void
	 */
	private function setXPath() {
		$this->_xpath = new DOMXPath($this->getDocument());
		$this->getXPath()->registerNamespace("ode", $this->getDocument()->lookupNamespaceUri("ode"));
	}
	
	/**
	 * Retrieves the XPath object for querying
	 * tag elements in the XML template
	 * 
	 * @access private
	 * @return DOMXPath
	 */
	private function getXPath() {
		return $this->_xpath;
	}
	
	/**
	 * Sets the DOM document object
	 * 
	 * @access private
	 * @return void
	 */
	private function setDocument() {
		$this->_doc = new DOMDocument("1.0", "UTF-8");
		$this->getDocument()->load($this->getPath() . DIRECTORY_SEPARATOR . $this->getLayout());
	}
	
	/**
	 * Retrieves the DOM document object
	 * 
	 * @access private
	 * @return DOMDocument
	 */
	private function getDocument() {
		return $this->_doc;
	}
	
	/**
	 * Sets the path to all template files
	 * 
	 * @param string $path
	 * @access private
	 * @return void
	 */
	private function setPath($path) {
		$this->_path = $path;
	}
	
	/**
	 * Retrieves the full path to the template directory
	 * 
	 * @access private
	 * @return string
	 */
	public function getPath() {
		return $this->_path;
	}
	
	/**
	 * Sets a template variable from PHP
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @access public
	 * @return void
	 */
	public function set($name, $value) {
		$this->_variables->add($name, new Ode_Template_Variable($name, $value));
	}
	
	/**
	 * Retrieves a specified template variable
	 * that was established in PHP
	 * 
	 * @param string $name
	 * @access public
	 * @return mixed
	 */
	public function get($name) {
		return $this->_variables->get($name);
	}
}