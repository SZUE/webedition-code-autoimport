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
							$searching = " LIKE '%" . $this->db->escape($searchname[$i]) . "' ";
							$sql .= $this->sqlwhere($searchfield[$i], $searching, null);
							break;
						case 'START':
							$searching = " LIKE '" . $this->db->escape($searchname[$i]) . "%' ";
							$sql .= $this->sqlwhere($searchfield[$i], $searching, null);
							break;
						case 'IN':
							$tmp = array_map('trim', explode(',', $searchname[$i]));
							$searching = ' IN ("' . implode('","', $tmp) . '") ';
							$sql .= $this->sqlwhere($searchfield[$i], $searching, null);
							break;
						case 'IS':
							$searching = "='" . $this->db->escape($searchname[$i]) . "' ";
							$sql .= $this->sqlwhere($searchfield[$i], $searching, null);
							break;
						case '<':
						case '<=':
						case '>':
						case '>=':
							$searching = ' ' . $searchlocation[$i] . " '" . $this->db->escape($searchname[$i]) . "' ";
							$sql .= $this->sqlwhere($searchfield[$i], $searching, null);
							break;
						default :
							$searching = " LIKE '%" . $this->db->escape($searchname[$i]) . "%' ";
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

		if($this->table){
			$this->where = ($where ? : ($this->where ? : '1'));
			return f('SELECT COUNT(1) FROM ' . $this->db->escape($this->table) . ' WHERE ' . $this->where, '', $this->db);
		}
		return -1;
	}

	function searchquery($where = '', $get = '*', $table = '', $order = '', $limit = ''){

		$this->table = ($table ? : ($this->table ? : ''));

		if($this->table){
			$this->where = (empty($where)) ? ((empty($this->where)) ? '' : ' WHERE ' . $this->where) : ' WHERE ' . $where;
			$this->get = (empty($get)) ? ((empty($this->get)) ? '*' : $this->get) : $get;
			$this->Order = (!empty($order)) ? $order : $this->Order;
			$order = ((empty($this->Order)) ? '' : ' ORDER BY ' . $this->Order);

			$this->limit = ' ' . $this->searchstart . ',' . $this->anzahl . ' ';

			$this->limit = ' LIMIT ' . ($limit ? : $this->limit);

			$this->db->query('SELECT ' . rtrim($this->get, ',') . ' FROM ' . $this->db->escape($this->table) . ' ' . $this->where . ' ' . $order . ' ' . $this->limit);
		} else {
			return -1;
		}
	}

	function setlimit($anzahl = '', $searchstart = ''){
		$this->anzahl = ($anzahl ? : ($this->anzahl ? : $this->defaultanzahl));
		$this->searchstart = ($searchstart ? : ($this->searchstart ? : '0'));

		$this->limit = ' ' . $this->searchstart . ',' . $this->anzahl . ' ';

		return $this->limit;
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

	function getJSinWElistnavigation($name){
		return $this->getJSinWEforwardbackward($name) . $this->getJSinWEorder($name);
	}

	function getJSinWEforwardbackward($name){
		return we_html_element::jsScript(JS_DIR . 'tooltip.js') . we_html_element::jsElement('
_EditorFrame.setEditorIsHot(false);

function next(){
	document.we_form.elements[\'SearchStart\'].value = parseInt(document.we_form.elements[\'SearchStart\'].value) + ' . $this->anzahl . ';
	top.we_cmd("reload_editpage");
}
function back(){
	document.we_form.elements[\'SearchStart\'].value = parseInt(document.we_form.elements[\'SearchStart\'].value) - ' . $this->anzahl . ';
	top.we_cmd("reload_editpage");
}');
	}

	function getJSinWEorder($name){
		return we_html_element::jsElement('
function setOrder(order){

	foo = document.we_form.elements[\'Order\'].value;

	if(((foo.substring(foo.length-5,foo.length) == " DESC") && (foo.substring(0,order.length-5) == order)) || foo != order){
		document.we_form.elements[\'Order\'].value=order;
	}else{
		document.we_form.elements[\'Order\'].value=order+" DESC";
	}
	top.we_cmd("reload_editpage");
}');
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
		$out = '<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td>' .
			($this->searchstart ?
				we_html_button::create_button('back', 'javascript:back();') : //bt_back
				we_html_button::create_button('back', '', true, 100, 22, '', '', true)
			) . '
	</td>
	<td>' . we_html_tools::getPixel(10, 2) . '</td>
	<td class="defaultfont"><b>' . (($we_search_anzahl) ? $this->searchstart + 1 : 0) . '-' .
			(($we_search_anzahl - $this->searchstart) < $this->anzahl ?
				$we_search_anzahl :
				$this->searchstart + $this->anzahl) .
			' ' . g_l('global', '[from]') . ' ' . $we_search_anzahl . '</b></td>
	<td>' . we_html_tools::getPixel(10, 2) . '</td>
	<td>' .
			(($this->searchstart + $this->anzahl) < $we_search_anzahl ?
				we_html_button::create_button("next", "javascript:next();") : //bt_back

				we_html_button::create_button("next", "", true, 100, 22, "", "", true)) .
			'</td>
	<td>' . we_html_tools::getPixel(10, 2) . '</td>
	<td>';

		$pages = array();
		for($i = 0; $i < ceil($we_search_anzahl / $this->anzahl); $i++){
			$pages[($i * $this->anzahl)] = ($i + 1);
		}

		$page = ceil($this->searchstart / $this->anzahl) * $this->anzahl;

		$select = we_html_tools::htmlSelect("page", $pages, 1, $page, false, array("onchange" => "this.form.elements['SearchStart'].value = this.value;we_cmd('reload_editpage');"));
		if(!defined('SearchStart')){
			define("SearchStart", true);
			$out .= we_html_tools::hidden("SearchStart", $this->searchstart);
		}

		$out .= $select . '</td></tr></table>';
		return $out;
	}

	function next_record(){
		$ret = $this->db->next_record();
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
