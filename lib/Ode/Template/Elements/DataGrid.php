<?php
/**
 * Template engine's data grid element <ode:DataGrid />
 * 
 * Provides the the HTML content for a data grid to display
 * data in a tabular format.
 * 
 * @author cjwalsh
 * @copyright Christopher Walsh 2010
 * @package Ode_Template_Elements
 * @name DataGrid
 *
 */
class Ode_Template_Elements_DataGrid extends Ode_Template_Element {
	const TAG_DATAGRIDCOLUMN = "DataGridColumn";
	
	const ATTRIBUTE_DATASOURCE = "dataSource";
	
	const ATTRIBUTE_DATAFIELD = "dataField";
	
	/**
	 * 
	 * @var HTML_Table
	 */
	private $_table;
	
	private $_doc;
	
	private $_xpath;
	
	private $_columns;
	
	/**
	 * Constructor
	 * 
	 * @param DOMNode $node
	 * @access public
	 * @return void
	 */
	public function __construct(DOMNode $node) {
		$this->setNode($node);
		$this->setParent($node->parentNode);
		
		$this->setDoc($node);
		
		$this->setXPath();
		
		$this->setTable();
		
		$this->setColumns();
		
		$this->setRows();
		
		$this->setDOM();
		
		$this->getParent()->replaceChild($this->getParent()->appendChild($this->getNode()->ownerDocument->importNode($this->getDOM()->firstChild, true)), $this->getNode());
	}
	
	private function setXPath() {
		$this->_xpath = new DOMXPath($this->getDoc());
		$this->_xpath->registerNamespace("ode", $this->getDoc()->lookupNamespaceUri("ode"));
	}
	
	private function getXPath() {
		return $this->_xpath;
	}
	
	private function setDoc(DOMNode $node) {
		$doc = new DOMDocument();
		$doc->appendChild($doc->importNode($node, true));
		
		$this->_doc = $doc;
	}
	
	private function getDoc() {
		return $this->_doc;
	}
	
	/**
	 * Sets the HTML_Table object to be reused by this instance
	 * 
	 * @access private
	 * @return void
	 */
	private function setTable() {
		$this->_table = new HTML_Table();
		//@todo delete this after development
		$this->getTable()->setAttribute("class", "data");
		$this->getTable()->setAttribute("cellspacing", 0);
	}
	
	/**
	 * Retrieves this instance's HTML_Table object
	 * 
	 * @access private
	 * @return HTML_Table
	 */
	private function getTable() {
		return $this->_table;
	}
	
	/**
	 * Sets the HTML DOM for the template to be rendered
	 * 
	 * @access private
	 * @return void
	 */
	public function setDOM() {

		$tableDOM = new DOMDocument();
		$tableDOM->loadXML($this->getTable()->toHtml());
		
		$this->_DOM = $tableDOM;
	}
	
	/**
	 * Retrieves table's HTML DOM
	 * 
	 * @access private
	 * @return DOMDocument
	 */
	public function getDOM() {
		return $this->_DOM;
	}
	
	private function setColumns() {
		$columns = new Ode_Template_Column_Collection();
		
		$cNodes = $this->getXPath()->query(Ode_Template_Engine::NS_PREFIX . "Columns/" . Ode_Template_Engine::NS_PREFIX . "DataGridColumn");
		for($i=0; $i<$cNodes->length; $i++) {
			//build HTML
			$this->getTable()->setHeaderContents(0, $i, $cNodes->item($i)->textContent);
			
			// store for use by rows
			$columns->add(UUID::get(), new Ode_Template_Column($cNodes->item($i)->textContent, $cNodes->item($i)->getAttribute(self::ATTRIBUTE_DATAFIELD)));
		}
		
		$this->_columns = $columns;
	}
	
	private function getColumns() {
		return $this->_columns;
	}
	
	private function setRows() {
		if($this->getNode()->getAttribute(self::ATTRIBUTE_DATASOURCE) != "") {
			// use PHP-derived variable
		} else {
			$dsNodes = $this->getXPath()->query(Ode_Template_Engine::NS_PREFIX . "DataSource/" . Ode_Template_Engine::NS_PREFIX . "Object");
			for($i=0; $i<$dsNodes->length; $i++) {
				$cIterator = $this->getColumns()->getIterator();
				for($j=0; $j<$cIterator->count(); $j++) {
					$this->getTable()->setCellContents(($i+1), $j, $dsNodes->item($i)->getAttribute($cIterator->current()->getDataField()));
					
					$cIterator->next();
				}
			}
		}
	}
}