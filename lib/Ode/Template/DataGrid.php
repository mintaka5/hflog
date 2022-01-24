<?php
class Ode_Template_DataGrid {
	private $_columns;
	
	private $_rows;
	
	public function __construct(Ode_Template_Column_Collection $columns = null, Ode_Template_Row_Collection $rows = null) {
		if(!is_null($columns)) {
			$this->setColumns($columns);
		}
		
		if(!is_null($rows)) {
			$this->setRows($rows);
		}
	}
	
	public function setRows(Ode_Template_Row_Collection $rows) {
		$this->_rows = $rows;
	}
	
	public function getRows() {
		return $this->_rows;
	}
	
	public function setColumns(Ode_Template_Column_Collection $columns) {
		$this->_columns = $columns;
	}
	
	public function getColumns() {
		return $this->_columns;
	}
}