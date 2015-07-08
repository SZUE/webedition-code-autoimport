<?php
/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * Class we_html_table
 *
 * Provides functions for creating html tags used in forms.
 */
class we_html_table extends we_html_baseCollection{

	/**
	 * Constructor of class we_html_table
	 *
	 * @param      $attribs                                array
	 * @param      $rows_num                               int                 (optional)
	 * @param      $cols_num                               int                 (optional)
	 *
	 * @return     we_html_table
	 */
	function __construct(array $attribs = array(), $rows_num = 0, $cols_num = 0, array $content = null){
		parent::__construct('table', true, $attribs);
		if($rows_num){
			$this->addRow($rows_num);
			$this->addCol($cols_num);
		}
		$this->setTableContent($content);
	}

	public function setTableContent(array $content = null){
		if($content){
			foreach($content as $rowNo => $rowContent){
				foreach($rowContent as $colNo => $col){
					$this->setCol($rowNo, $colNo, $col[0], $col[1]);
				}
			}
		}
	}

	/**
	 * This function adds a row to the table
	 *
	 * @param      $rows_num                               int                 (optional)
	 *
	 * @see        we_html_table()
	 *
	 * @return     void
	 */
	function addRow($rows_num = 1){
		$cols_num = 0;
		if(isset($this->childs)){
			if(array_key_exists(0, $this->childs)){
				if(is_array($this->childs[0]->childs)){
					$cols_num = count($this->childs[0]->childs);
				}
			}
		}
		for($i = 0; $i < $rows_num; $i++){
			$this->childs[] = new we_html_baseCollection("tr");
			for($j = 0; $j < $cols_num; $j++){
				$this->childs[count($this->childs) - 1]->childs[] = new we_html_baseElement("td");
			}
		}
	}

	/**
	 * This function adds a column to the table
	 *
	 * @param      $cols_num                               int                 (optional)
	 *
	 * @see        we_html_table()
	 *
	 * @return     void
	 */
	function addCol($cols_num = 1){
		for($i = 0; $i < $cols_num; $i++){
			foreach($this->childs as &$v){
				$v->childs[] = new we_html_baseElement('td');
			}
		}
	}

	/**
	 * This functions sets the current row being edited
	 *
	 * @param      $rowid                                  int
	 * @param      $attribs                                array
	 * @param      $cols_num                               int                 (optional)
	 *
	 * @return     void
	 */
	function setRow($rowid, $attribs = array(), $cols_num = 0){
		$row = & $this->getChild($rowid);
		$row->setAttributes($attribs);

		if($cols_num){
			if($cols_num > count($row->childs)){
				$row->addChild(new we_html_baseElement('td'));
			} else if($cols_num < count($row->childs)){
				$row->childs = array_splice($row->childs, ($cols_num - 1));
			}
		}
	}

	/**
	 * This function sets the current column being edited
	 *
	 * @param      $rowid                                  int
	 * @param      $colid                                  int
	 * @param      $attribs                                array               (optional)
	 * @param      $content                                string              (optional)
	 *
	 * @return     void
	 */
	function setCol($rowid, $colid, $attribs = array(), $content = ''){
		while(!isset($this->childs[$rowid])){
			$this->addRow();
		}
		$col = $this->getChild($rowid)->getChild($colid);
		$col->setAttributes($attribs);
		$col->setContent($content);
	}

	/**
	 * Assigns the attributes of a column
	 *
	 * @param      $rowid                                  int
	 * @param      $colid                                  int
	 * @param      $attribs                                array               (optional)
	 *
	 * @return     void
	 */
	function setRowAttributes($rowid, $attribs = array()){
		$row = & $this->getChild($rowid);
		$row->setAttributes($attribs);
	}

	/**
	 * Assigns the attributes of a column
	 *
	 * @param      $rowid                                  int
	 * @param      $colid                                  int
	 * @param      $attribs                                array               (optional)
	 *
	 * @return     void
	 */
	function setColAttributes($rowid, $colid, $attribs = array()){
		$col = & $this->getChild($rowid)->getChild($colid);
		$col->setAttributes($attribs);
	}

	/**
	 * Sets the content of a column
	 *
	 * @param      $rowid                                  int
	 * @param      $colid                                  int
	 * @param      $content                                string              (optional)
	 *
	 * @return     void
	 */
	function setColContent($rowid, $colid, $content = ""){
		$col = & $this->getChild($rowid)->getChild($colid);
		$col->setContent($content);
	}

	/**
	 * Returns the rendered HTML code
	 *
	 * @return     string
	 */
	function getHtml($isCopy = false){
		if($isCopy){
			return parent::getHtml();
		}

		$copy = $this->copy();
		$rows_num = count($copy->childs);

		for($i = 0; $i < $rows_num; $i++){
			$row = & $copy->getChild($i);
			$cols_num = count($row->childs);
			$colspan = 0;
			for($j = 0; $j < $cols_num; $j++){
				if($colspan){
					$row->delChild($j);
					$j--;
					$cols_num = count($row->childs);
					$colspan--;
				} else {
					$col = $row->getChild($j);
					if(in_array('colspan', array_keys($col->attribs))){
						$colspan = $col->getAttribute('colspan') - 1;
					}
				}
			}
		}

		return $copy->getHTML(true);
	}

}
