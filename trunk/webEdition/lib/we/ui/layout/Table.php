<?php
/**
 * webEdition SDK
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package none
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * Table Class to layout elements. It renders a normal HTML table
 *
 * @category   we
 * @package none
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_ui_layout_Table extends we_ui_abstract_AbstractElement{
	/**
	 * Two dimensional array to hold the HTML for the cells
	 *
	 * @var array
	 */
	protected $_cellHTML = array();

	/**
	 * Two dimensional array to hold the attributes for the cells
	 *
	 * @var array
	 */
	protected $_cellAttributes = array();

	/**
	 * Pointer to the current row
	 *
	 * @var integer
	 */
	protected $_row = 0;

	/**
	 * Pointer to the current column
	 *
	 * @var integer
	 */
	protected $_column = 0;

	/**
	 * Adds an Element to the current cell,
	 * which is defined by the row and column pointer.
	 * If the $column or $row parameter is set,
	 * the column pointers will be updated before inserting the element
	 *
	 * @param we_ui_abstract_AbstractElement $elem element to insert
	 * @param integer $column
	 * @param integer $row
	 * @return void
	 */
	public function addElement($elem, $column = -1, $row = -1){
		$this->addCSSFiles($elem->getCSSFiles());
		$this->addJSFiles($elem->getJSFiles());

		if($column == -1){
			$column = $this->_column;
		} else {
			$this->_column = $column;
		}
		if($row == -1){
			$row = $this->_row;
		} else {
			$this->_row = $row;
		}
		if(!isset($this->_cellHTML[$row])){
			$this->_cellHTML[$row] = array();
		}
		if(!isset($this->_cellHTML[$row][$column])){
			$this->_cellHTML[$row][$column] = $elem->getHTML();
		} else {
			$this->_cellHTML[$row][$column] .= $elem->getHTML();
		}
	}

	/**
	 * Adds HTML to the current cell,
	 * which is defined by the row and column pointer.
	 * If the $column or $row parameter is set,
	 * the column pointers will be updated before inserting the HTML
	 *
	 * @param string $html element to insert
	 * @param integer $column
	 * @param integer $row
	 * @return void
	 */
	public function addHTML($html, $column = -1, $row = -1){
		if($column == -1){
			$column = $this->_column;
		} else {
			$this->_column = $column;
		}
		if($row == -1){
			$row = $this->_row;
		} else {
			$this->_row = $row;
		}
		if(!isset($this->_cellHTML[$row])){
			$this->_cellHTML[$row] = array();
		}
		if(!isset($this->_cellHTML[$row][$column])){
			$this->_cellHTML[$row][$column] = $html;
		} else {
			$this->_cellHTML[$row][$column] .= $html;
		}
	}

	/**
	 * Sets the attributes for the current cell,
	 * which is defined by the row and column pointer.
	 * If the $column or $row parameter is set,
	 * the column pointers will be updated before setting the attributes
	 *
	 * @param array $attributes associative array with attributes to insert
	 * @param integer $column
	 * @param integer $row
	 * @return void
	 */
	public function setCellAttributes($attributes, $column = -1, $row = -1){
		if($column == -1){
			$column = $this->_column;
		} else {
			$this->_column = $column;
		}
		if($row == -1){
			$row = $this->_row;
		} else {
			$this->_row = $row;
		}
		if(!isset($this->_cellAttributes[$row])){
			$this->_cellAttributes[$row] = array();
		}
		if(!isset($this->_cellAttributes[$row][$column])){
			$this->_cellAttributes[$row][$column] = $attributes;
		} else {
			$this->_cellAttributes[$row][$column] = array_merge($this->_cellAttributes[$row][$column], $attributes);
		}
	}

	/**
	 * Sets the row pointer to the next row
	 *
	 * @param boolean $resetColumn if set to true the column pointer will be reset to 0
	 * @return void
	 */
	public function nextRow($resetColumn = false){
		$this->_row = $this->_row + 1;
		if($resetColumn){
			$this->resetColumn();
		}
	}

	/**
	 * Sets the column pointer to the next column
	 *
	 * @return void
	 */
	public function nextColumn(){
		$this->_column = $this->_column + 1;
	}

	/**
	 * Reset the row pointer to 0
	 *
	 * @return void
	 */
	public function resetRow(){
		$this->_row = 0;
	}

	/**
	 * Reset the column pointer to 0
	 *
	 * @return void
	 */
	public function resetColumn(){
		$this->_column = 0;
	}

	/**
	 * Retrieve the column pointer
	 *
	 * @return integer
	 */
	public function getColumn(){
		return $this->_column;
	}

	/**
	 * Retrieve the row pointer
	 *
	 * @return integer
	 */
	public function getRow(){
		return $this->_row;
	}

	/**
	 * Sets the column pointer
	 *
	 * @param $column integer
	 * @return void
	 */
	public function setColumn($column){
		$this->_column = $column;
	}

	/**
	 * Sets the row pointer
	 *
	 * @param $row integer
	 * @return void
	 */
	public function setRow($row){
		$this->_row = $row;
	}

	/**
	 * Renders and returns the HTML
	 *
	 * @return string
	 */
	public function _renderHTML(){
		$html = '<table class="default" ' . $this->_getNonBooleanAttribs('id') . $this->_getComputedStyleAttrib() . $this->_getComputedClassAttrib() . '>';

		$maxRowIndex = -1;
		$maxColIndex = -1;
		foreach($this->_cellHTML as $rowIndex => $cols){
			$maxRowIndex = max($maxRowIndex, $rowIndex);
			foreach($cols as $colIndex => $col){
				$maxColIndex = max($maxColIndex, $colIndex);
			}
		}

		$colspan = 1;

		for($row = 0; $row <= $maxRowIndex; $row++){
			$colspan = 1;
			$html .= '<tr>';
			for($col = 0; $col <= $maxColIndex; $col++){
				if($colspan < 2){
					if(isset($this->_cellAttributes[$row][$col])){
						if(isset($this->_cellAttributes[$row][$col]['colspan'])){
							$colspan = abs($this->_cellAttributes[$row][$col]['colspan']);
						}
						$attribs = $this->_cellAttributes[$row][$col];
						$html .= getHtmlTag('td', $attribs, '', false, true);
					} else {
						$html .= '<td style="vertical-align:top">';
					}
					if(isset($this->_cellHTML[$row][$col])){
						$html .= $this->_cellHTML[$row][$col];
					}
					$html .= '</td>';
				} else {
					$colspan--;
				}
			}
			$html .= '</tr>';
		}

		$html .= '</table>';
		return $html;
	}

	/**
	 * Retrieve border attribute
	 *
	 * @return integer
	 */
	public function getBorder(){
		t_e('deprecated', __FUNCTION__);
		return 0;
	}

	public function getCellPadding(){
		t_e('deprecated', __FUNCTION__);
		return 0;
	}

	public function getCellSpacing(){
		t_e('deprecated', __FUNCTION__);
		return 0;
	}

	/**
	 * Sets the border attribute
	 *
	 * @param integer $border
	 * @return void
	 */
	public function setBorder(){
		t_e('deprecated', __FUNCTION__);
	}

	public function setCellPadding(){
		t_e('deprecated', __FUNCTION__);
	}

	public function setCellSpacing(){
		t_e('deprecated', __FUNCTION__);
	}

}
