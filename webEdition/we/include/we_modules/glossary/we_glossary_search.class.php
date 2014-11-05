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
class we_glossary_search{

	/**
	 * Database Object DB_WE
	 *
	 * @var object
	 */
	var $DatabaseObject = null;

	/**
	 * Tablename
	 *
	 * @var string
	 */
	var $Table = "";

	/**
	 * fields which have to be selected
	 *
	 * @var array
	 */
	var $Fields = array();

	/**
	 * where clause
	 *
	 * @var string
	 */
	var $Where = "";

	/**
	 * group by clause
	 *
	 * @var string
	 */
	var $GroupBy = "";

	/**
	 * having clause
	 *
	 * @var string
	 */
	var $Having = "";

	/**
	 * order clause
	 *
	 * @var string
	 */
	var $Order = "";

	/**
	 * offset of the queryresult
	 *
	 * @var integer
	 */
	var $Offset = 0;

	/**
	 * Rows of the result
	 *
	 * @var integer
	 */
	var $Rows = 10;

	/**
	 * PHP 5 Constructor
	 *
	 */
	function __construct($table){
		$this->DatabaseObject = new DB_WE();
		$this->Table = $table;
	}

	/**
	 * set the fields wich have to be selected
	 *
	 * @param array $fields
	 */
	function setFields($fields = array()){

		$this->Fields = $fields;
	}

	/**
	 * set the where clause
	 *
	 * @param string $where
	 */
	function setWhere($where = ""){
		$this->Where = $where;
	}

	/**
	 * set the group by clause
	 *
	 * @param string $groupBy
	 */
	function setGroupBy($groupBy = ""){
		$this->GroupBy = $groupBy;
	}

	/**
	 * set the where clause
	 *
	 * @param string $having
	 */
	function setHaving($having = ""){
		$this->Having = $having;
	}

	/**
	 * set the order clause
	 *
	 * @param string $order
	 */
	function setOrder($order = "", $sort = "ASC"){
		$this->Order = $order;
		$this->Sort = $sort;
	}

	/**
	 * set the offset and the count
	 *
	 * @param integer $offset
	 * @param integer $rows
	 */
	function setLimit($offset = 0, $rows = 10){
		$this->Offset = $offset;
		$this->Rows = $rows;
	}

	/**
	 * get statement
	 *
	 * @param boolean $countStmt
	 * @return string
	 */
	function _getStatement($countStmt = false){
		$stmt = 'SELECT ' .
			($countStmt ? 'COUNT(1)' : implode(', ', $this->Fields)) .
			' FROM ' . escape_sql_query($this->Table) . ' ' .
			'WHERE ' . ($this->Where ? : '1') .
			($this->GroupBy ? ' GROUP BY ' . $this->GroupBy : '') .
			($this->Having ? ' HAVING ' . $this->Having : '');

		if(!$countStmt){
			if($this->Order != ''){
				$stmt .= ' ORDER BY ' . $this->Order . ' ' . $this->Sort;
			}

			$stmt .= ' LIMIT ' . $this->Offset . ', ' . $this->Rows;
		}

		return trim($stmt);
	}

	/**
	 * count the items
	 *
	 * @return integer
	 */
	function countItems(){
		return f($this->_getStatement(true), 'COUNT(1)', $this->DatabaseObject);
	}

	/**
	 * execute the saerch query
	 *
	 */
	function execute(){
		$this->DatabaseObject->query($this->_getStatement());
	}

	/**
	 * iterate over the whole resultset
	 *
	 * @return mixed
	 */
	function next(){
		return $this->DatabaseObject->next_record();
	}

	/**
	 * get the value of a field
	 *
	 * @param string $field
	 * @return mixed
	 */
	function getField($field){
		return $this->DatabaseObject->f($field);
	}

	/**
	 * get the pages as array (key = pageNr, value = start)
	 *
	 * @return array
	 */
	function getPages(){
		$_count = $this->countItems();
		$_pages = ceil($_count / $this->Rows);

		$pages = array();
		for($i = 1; $i <= $_pages; $i++){
			$pages[($i - 1) * $this->Rows] = $i;
		}

		return $pages;
	}

	/**
	 * get the number of the active page
	 *
	 * @return integer
	 */
	function getActivePage(){
		return ceil(($this->Offset - 1) / $this->Rows);
	}

}
