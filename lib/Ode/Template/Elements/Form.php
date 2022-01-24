<?php
/**
 * Template engine's Form element <ode:Form />
 * 
 * Primarily, provides HTML content generated from
 * the template engine's XML element, and will include
 * all child elements, which are for producing the form's
 * input elements.
 * 

 * There are 3 ways to establish data for select boxes, radio buttons, or
 * checkboxes: 1) XML element DataSource; 2) an option collection 
 * (Ode_Template_Options_Collection) through Ode_Template_Engine assignment;
 * 3) an Ode_Template_Engine assignment using a standard 1-dimensional associative
 * array.
 * 
 * <code>
 * <!-- XML DataSource -->
 * <ode:Select name="testSelect">
 * 	<ode:Label>Test Select</ode:Label>
 * 	<ode:DataSource>
 * 		<ode:Item value="test1">Test 1</ode:Item>
 * 		<ode:Item value="test2">Test 2</ode:Item>
 * 		<ode:Item value="test3">Test 3</ode:Item>
 * 	</ode:DataSource>
 * </ode:Select>
 * 
 * <?php
 * // Ode_Template_Options_Collection sample
 * new Ode_Template_Options_Collection(
 * 	new Ode_Template_Option("test1", "Test 1"),
 * 	new Ode_Template_Option("test2", "Test 2"),
 * 	new Ode_Template_Option("test3", "Test 3")
 * );
 * ?>
 * 
 * <?php
 * // Associative array sample
 * array(
 *	"chk1" => "What?",
 *	"chk2" => "Why?",
 *	"chk3" => "Where?"
 * )
 * ?>
 * </code>
 * 
 * @package Ode_Template_Elements
 * @name Form
 * @author cjwalsh
 * @copyright Christopher Walsh 2010
 *
 */
class Ode_Template_Elements_Form extends Ode_Template_Element {
	/**
	 * 
	 * @var string
	 * @access public
	 */
	const ATTRIBUTE_NAME = "name";
	
	/**
	 * 
	 * @var string
	 * @access public
	 */
	const ATTRIBUTE_PROCESSOR = "processor";
	
	/**
	 * 
	 * @var string
	 * @access public
	 */
	const ATTRIBUTE_DATASOURCE = "dataSource";
	
	/**
	 * 
	 * @var string
	 * @access public
	 */
	const ATTRIBUTE_ITEM_VALUE = "value";
	
	/**
	 * 
	 * @var string
	 * @access public
	 */
	const TAG_TEXTFIELD = "TextField";
	
	/**
	 * 
	 * @var string
	 * @access public
	 */
	const TAG_SELECT = "Select";
	
	/**
	 * 
	 * @var string
	 * @access public
	 */
	const TAG_RADIO = "RadioButtons";
	
	/**
	 * 
	 * @var string
	 * @access public
	 */
	const TAG_CHECKBOXES = "CheckBoxes";
	
	/**
	 * 
	 * @var string
	 * @access public
	 */
	const TAG_BUTTON = "Button";
	
	/**
	 * 
	 * @var string
	 * @access public
	 */
	const TAG_DATASOURCE = "DataSource";
	
	/**
	 * 
	 * @var string
	 * @access public
	 */
	const TAG_DATASOURCE_ITEM = "Item";
	
	/**
	 * 
	 * @var string
	 * @access public
	 */
	const TAG_LABEL = "Label";
	
	/**
	 * 
	 * @var HTML_QuickForm
	 * @access private
	 */
	private $_form;
	
	/**
	 * Constructor
	 * 
	 * @param DOMNode $node
	 * @access public
	 * @return void
	 */
	public function __construct(DOMNode $node) {
		$this->setNode($node);
		$this->setParent($this->getNode()->parentNode);
		
		$this->setForm();
		
		$this->setDOM();
		
		$this->getParent()->replaceChild($this->getParent()->appendChild($this->getNode()->ownerDocument->importNode($this->getDOM()->firstChild, true)), $this->getNode());
	}
	
	/**
	 * Sets the element's form HTML generation object
	 * 
	 * @return void
	 * @access private
	 */
	private function setForm() {
		$this->_form = new HTML_QuickForm($this->getNode()->getAttribute(self::ATTRIBUTE_NAME), "post", $this->getNode()->getAttribute(self::ATTRIBUTE_PROCESSOR));
	}
	
	/**
	 * Retrieves the element's form HTML generation object
	 * 
	 * @access private
	 * @return HTML_QuickForm
	 */
	private function getForm() {
		return $this->_form;
	}
	
	/**
	 * Sets the element's form HTML DOM document object
	 * 
	 * @access private
	 * @return void
	 */
	public function setDOM() {
		foreach($this->getNode()->childNodes as $element) {
			if($element instanceof DOMElement) {
				switch($element->nodeName) {
					default:
					case (Ode_Template_Engine::NS_PREFIX . self::TAG_TEXTFIELD):
						$field = new HTML_QuickForm_text();
						$field->setLabel($element->textContent);
						$field->setName($element->getAttribute(self::ATTRIBUTE_NAME));
						
						$this->getForm()->addElement($field);
						break;
					case (Ode_Template_Engine::NS_PREFIX . self::TAG_SELECT):
						$field = new HTML_QuickForm_select();
						$field->setLabel($element->getElementsByTagName(self::TAG_LABEL)->item(0)->textContent);
						$field->setName($element->getAttribute(self::ATTRIBUTE_NAME));
						
						$optionData = $this->getData($element)->getIterator();
						while($optionData->valid()) {
							$field->addOption($optionData->current()->getTitle(), $optionData->current()->getValue());
							$optionData->next();
						}
						
						$this->getForm()->addElement($field);
						break;
					case (Ode_Template_Engine::NS_PREFIX . self::TAG_BUTTON):
						$field = new HTML_QuickForm_submit();
						$field->setValue($element->textContent);
						
						$this->getForm()->addElement($field);
						break;
					case (Ode_Template_Engine::NS_PREFIX . self::TAG_CHECKBOXES):
						$fields = array();
						
						$optionData = $this->getData($element)->getIterator();
						while($optionData->valid()) {
							$field = new HTML_QuickForm_checkbox();
							$field->setText($optionData->current()->getTitle());
							$field->setName($optionData->current()->getValue());
							$fields[] = $field;
							
							$optionData->next();
						}
						
						$this->getForm()->addGroup($fields, $element->getAttribute(self::ATTRIBUTE_NAME), $element->getElementsByTagName(self::TAG_LABEL)->item(0)->textContent, " ");
						break;
					case (Ode_Template_Engine::NS_PREFIX . self::TAG_RADIO):
						$fields = array();
						
						$optionData = $this->getData($element)->getIterator();
						while($optionData->valid()) {
							$field = new HTML_QuickForm_radio();
							$field->setText($optionData->current()->getTitle());
							$field->setValue($optionData->current()->getValue());
							$fields[] = $field;
							
							$optionData->next();
						}
						
						$this->getForm()->addGroup($fields, $element->getAttribute(self::ATTRIBUTE_NAME), $element->getElementsByTagName(self::TAG_LABEL)->item(0)->textContent, " ");
						break;
				}
			}
		}
		
		$formDOM = new DOMDocument();
		$formDOM->loadXML($this->getForm()->toHtml());
		
		$this->_DOM = $formDOM;
	}
	
	/**
	 * Retrieves the element's form HTML DOM document object
	 * 
	 * @access private
	 * @return DOMDocument
	 */
	public function getDOM() {
		return $this->_DOM;
	}
	
	/**
	 * Retrieves the data needed to supply select menus, checkboxes, or radio
	 * buttons with specified options, from either a PHP template assignment
	 * or provided through the XML template's DataSource element.
	 * 
	 * @param DOMNode $node
	 * @access private
	 * @return Ode_Template_Options_Collection
	 */
	private function getData(DOMNode $node) {
		$ds = new Ode_Template_Options_Collection();
		
		if($node->getAttribute(self::ATTRIBUTE_DATASOURCE) != "") {
			$data = Ode_Template_Engine::getInstance()->get($node->getAttribute(self::ATTRIBUTE_DATASOURCE))->getValue();
			
			if($data instanceof Ode_Template_Options_Collection) {
				$ds = $data;
			} else if(is_array($data)) {
				foreach($data as $value => $label) {
					$ds->add($value, new Ode_Template_Option($label, $value));
				}
			} else {}
		} else {
			$itemNodes = $node->getElementsByTagName(self::TAG_DATASOURCE_ITEM);

			for($i=0; $i<$itemNodes->length; $i++) {
				$ds->add($itemNodes->item($i)->getAttribute(self::ATTRIBUTE_ITEM_VALUE), new Ode_Template_Option($itemNodes->item($i)->textContent, $itemNodes->item($i)->getAttribute(self::ATTRIBUTE_ITEM_VALUE)));
			}
		}
		
		return $ds;
	}
}