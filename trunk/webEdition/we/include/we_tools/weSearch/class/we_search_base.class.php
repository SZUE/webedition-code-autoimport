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
class we_search_base{
	protected $db;
	var $rows = -1;
	var $start = 0;
	var $order = '';
	var $desc = 0;
	var $tablename;
	var $GreenOnly;
	var $defaultanzahl = 10;
	var $where;
	var $get;
	var $Order;
	var $anzahl = 10;
	var $maxItems = 0;
	var $searchstart = 0;
	public $Record = array();

	function __construct(){
		$this->db = new DB_WE();
	}

	function init($sessDat = ''){
		foreach($sessDat as $cur){
			if(isset($GLOBALS['we_' . $this->Name . '_' . $cur])){
				$v = $GLOBALS['we_' . $this->Name . '_' . $cur];
				$this->$cur = $v;
			}
		}
	}

	// select expects an array with 	Searchid["field"] => "searchstring"
	// 									Searchid["search"] => "searchstring"
	//									Searchid["concat"] => "AND"
	//																("OR","XOR")
	//									Searchid["type"] => "START"
	//																("IS","END","CONTAIN","<","<=",">",..)
	function searchfor($searchname, $searchfield, $searchlocation, $tablename, $rows = -1, $start = 0, $order = '', $desc = 0){

		$this->tablename = $tablename;
		$i = 0;
		$sql = '';

		for($i = 0; $i < count($searchfield); $i++){

			if($searchname[$i]){
				$regs = explode('_', $searchfield[$i], 2); //bug #3694
				if((count($regs) == 2) && $regs[0] === 'date'){ //bug #3694
					$year = ($searchname[$i]['year'] && $searchname[$i]['year'] ? $searchname[$i]['year'] : date('Y'));
					$month = ($searchname[$i]['month'] && $searchname[$i]['month'] ? $searchname[$i]['month'] : '');
					$day = ($searchname[$i]['day'] && $searchname[$i]['day'] ? $searchname[$i]['day'] : '');
					$hour = ($searchname[$i]['hour'] && $searchname[$i]['hour'] ? $searchname[$i]['hour'] : '');
					$minute = ($searchname[$i]['minute'] && $searchname[$i]['minute'] ? $searchname[$i]['minute'] : '');

					$from = mktime(($hour ? : 0), ($minute ? : 0), 0, ($month ? : 1), ($day ? : 1), $year);
					$till = mktime(($hour ? : 23), ($minute ? : 59), 59, ($month ? : 12), ($day ? : date('t', mktime(0, 0, 0, ($month ? : 12), 1, $year))), $year);

					switch($searchlocation[$i]){
						case '<':
						case '<=':
						case '>':
						case '>=':
							$sql .= $this->sqlwhere($searchfield[$i], ' ' . $searchlocation[$i] . ' ' . $from . ' ', null);
							break;
						default :
							$sql .= $this->sqlwhere($searchfield[$i], ' BETWEEN ' . $from . ' AND ' . $till . ' ', null);
							break;
					}
				} else {

					switch($searchlocation[$i]){
						case 'END':
							$searching = ' LIKE "%' . $this->db->escape($searchname[$i]) . '" ';
							$sql .= $this->sqlwhere($searchfield[$i], $searching, null);
							break;
						case 'START':
							$searching = ' LIKE "' . $this->db->escape($searchname[$i]) . '%" ';
							$sql .= $this->sqlwhere($searchfield[$i], $searching, null);
							break;
						case 'IN':
							$tmp = array_map('trim', explode(',', $searchname[$i]));
							$searching = ' IN ("' . implode('","', $tmp) . '") ';
							$sql .= $this->sqlwhere($searchfield[$i], $searching, null);
							break;
						case 'IS':
							$searching = '="' . $this->db->escape($searchname[$i]) . '" ';
							$sql .= $this->sqlwhere($searchfield[$i], $searching, null);
							break;
						case 'LO':
							$searching = ' < "' . $this->db->escape($searchname[$i]) . '" ';
							$sql .= $this->sqlwhere($searchfield[$i], $searching, null);
							break;
						case 'LEQ':
							$searching = ' <= "' . $this->db->escape($searchname[$i]) . '" ';
							$sql .= $this->sqlwhere($searchfield[$i], $searching, null);
							break;
						case 'HI':
							$searching = ' > "' . $this->db->escape($searchname[$i]) . '" ';
							$sql .= $this->sqlwhere($searchfield[$i], $searching, null);
							break;
						case 'HEQ':
							$searching = ' >= "' . $this->db->escape($searchname[$i]) . '" ';
							$sql .= $this->sqlwhere($searchfield[$i], $searching, null);
							break;
						default :
							$searching = ' LIKE "%' . $this->db->escape($searchname[$i]) . '%" ';
							$sql .= $this->sqlwhere($searchfield[$i], $searching, null);
							break;
					}
				}
			}
		}

		return $sql;
	}

	function sqlwhere($we_SearchField, $searchlocation, $concat){
		$concat = (isset($concat)) ? $concat : 'AND';
		if(strpos($we_SearchField, ',') !== false){
			$foo = makeArrayFromCSV($we_SearchField);
			$q = array();
			foreach($foo as $f){
				$tmp = str_replace('.', '.`', $f);
				if($tmp == $f){
					$tmp = '`' . $tmp;
				}
				$q[] = $tmp . '` ' . $searchlocation;
			}
			return ' ' . $concat . ' ( ' . implode(' OR ', $q) . ' ) ';
		} else {
			$tmp = str_replace('.', '.`', $we_SearchField);
			if($tmp == $we_SearchField){
				$tmp = '`' . $tmp;
			}
			return ' ' . $concat . ' ' . $tmp . '` ' . $searchlocation . ' ';
		}
	}

	function countitems($where = '', $table = ''){
		$this->table = ($table ? : ($this->table ? : ''));

		if(!$this->table){
			return -1;
		}
		$this->where = ($where ? : ($this->where ? : '1'));
		return f('SELECT COUNT(1) FROM ' . $this->db->escape($this->table) . ' WETABLE WHERE ' . $this->where, '', $this->db);
	}

	function searchquery($where = '', $get = '*', $table = '', $order = '', $limit = ''){
		$this->table = ($table ? : $this->table);

		if(!$this->table){
			return -1;
		}
		$this->where = ($where? : $this->where);
		$this->get = ($get? : rtrim($this->get, ',')? : '*' );
		$this->Order = ($order? : $this->Order);
		$this->maxItems = f('SELECT COUNT(1) FROM ' . $this->table . ' ' . ($this->where ? ' WHERE ' . $this->where : ''));
		$this->limit = ' LIMIT ' . ($limit ? : $this->searchstart . ',' . $this->anzahl . ' ');

		$this->db->query('SELECT ' . $this->get . ' FROM ' . $this->table . ' ' . ($this->where ? ' WHERE ' . $this->where : '') . ' ' . ($this->Order ? ' ORDER BY ' . $this->Order : '') . ' ' . $this->limit);
	}

	function setlimit($anzahl = '', $searchstart = ''){
		$this->anzahl = ($anzahl ? : ($this->anzahl ? : $this->defaultanzahl));
		$this->searchstart = ($searchstart ? : ($this->searchstart ? : '0'));


		return ($this->limit = ' ' . $this->searchstart . ',' . $this->anzahl . ' ');
	}

	function getlimit(){
		return $this->limit;
	}

	function setstart($z){
		$this->searchstart = $z;
	}

	function setorder($z){
		$this->Order = $z;
	}

	function setwhere($z){
		$this->where = $z;
	}

	function settable($z){
		$this->table = $z;
	}

	function setget($z){
		$this->get = $z;
	}

	static function getLocation($name = 'locationField', $select = '', $size = 1){
		return we_html_tools::htmlSelect($name, we_search_search::getLocation(), $size, $select);
	}

	static function getLocationDate($name = 'locationField', $select = '', $size = 1){
		return we_html_tools::htmlSelect($name, we_search_search::getLocation('date'), $size, $select);
	}

	static function getLocationMeta($name = 'locationField', $select = '', $size = 1){
		return we_html_tools::htmlSelect($name, we_search_search::getLocation('meta'), $size, $select);
	}

	function getNextPrev($we_search_anzahl){
		t_e('call', $this->anzahl, $we_search_anzahl);
		$out = '<table class="default">
<tr>
	<td>' .
			($this->searchstart ?
				we_html_button::create_button(we_html_button::BACK, 'javascript:back();') : //bt_back
				we_html_button::create_button(we_html_button::BACK, '', true, 100, 22, '', '', true)
			) . '
	</td>
	<td class="defaultfont"><b>' . (($we_search_anzahl) ? $this->searchstart + 1 : 0) . '-' .
			(($we_search_anzahl - $this->searchstart) < $this->anzahl ?
				$we_search_anzahl :
				$this->searchstart + $this->anzahl) .
			' ' . g_l('global', '[from]') . ' ' . $we_search_anzahl . '</b></td>
	<td>' .
			(($this->searchstart + $this->anzahl) < $we_search_anzahl ?
				we_html_button::create_button(we_html_button::NEXT, "javascript:next();") : //bt_back

				we_html_button::create_button(we_html_button::NEXT, "", true, 100, 22, "", "", true)) .
			'</td>
	<td>';

		$pages = array();
		$maxPages = ceil($we_search_anzahl / $this->anzahl);
		for($i = 0; $i < $maxPages; $i++){
			$pages[($i * $this->anzahl)] = ($i + 1);
		}

		$page = ceil($this->searchstart / $this->anzahl) * $this->anzahl;

		$select = we_html_tools::htmlSelect("page", $pages, 1, $page, false, array("onchange" => "this.form.elements.SearchStart.value = this.value;we_cmd('reload_editpage');"));
		if(!defined('SearchStart')){//we need this, since pager is shown above & under the results
			define("SearchStart", true);
			$out .= we_html_element::htmlHidden("SearchStart", $this->searchstart);
		}

		return $out . $select . '</td></tr></table>';
	}

	function next_record(){
		$ret = $this->db->next_record(MYSQL_ASSOC);
		$this->Record = $this->db->Record;
		return $ret;
	}

	function f($Name){
		return $this->db->f($Name);
	}

	function getRecord(){
		return $this->Record;
	}

	function num_rows(){
		return $this->db->num_rows();
	}

	function escape($val){
		return $this->db->escape($val);
	}

}
