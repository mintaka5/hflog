<?php
class Ode_Template_Column {
	private $_dataField;
	private $_label;
	
	public function __construct($label = null, $dataField = null) {
		if(!is_null($label)) {
			$this->setLabel($label);
		}
		
		if(!is_null($dataField)) {
			$this->setDataField($dataField);
		}
	}
	
	public function setDataField($dataField) {
		$this->_dataField = $dataField;
	}
	
	public function getDataField() {
		return $this->_dataField;
	}
	
	public function setLabel($label) {
		$this->_label = $label;
	}
	
	public function getLabel() {
		return $this->_label;
	}
}