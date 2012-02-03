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
 * @package    webEdition_listview
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * class    we_listview
 * @desc    class for tag <we:listview>
 *
 */
class we_listview extends listviewBase{

	var $docType = ''; /* doctype string */
	var $IDs = array(); /* array of ids with pages which are found */
	var $casesensitive = false; /* set to true when a search should be case sensitive */
	var $ClassName = __CLASS__;
	var $contentTypes = '';
	var $searchable = true;
	var $condition = ''; /* condition string (like SQL) */
	var $defaultCondition = '';
	var $subfolders = true; // regard subfolders
	var $customers = '';
	var $languages = ''; //string of Languages, separated by ,
	var $numorder = false; // #3846
	

	/**
	 * we_listview()
	 * constructor of class
	 *
	 * @param   name          string  - name of listview
	 * @param   rows          integer - number of rows to display per page
	 * @param   offset        integer - start offset of first page
	 * @param   order         string  - field name(s) to order by
	 * @param   desc          boolean - set to true, if order should be descendend
	 * @param   docType       string  - doctype
	 * @param   cats          string  - comma separated categories
	 * @param   catOr         boolean - set to true if it should be an "OR condition"
	 * @param   casesensitive boolean - set to true when a search should be case sensitive
	 * @param   workspaceID   string - commaseperated list of id's of workspace
	 * @param   contentTypes  string  - contenttypes of documents (image,text ...)
	 * @param   cols   		  integer - to display a table this is the number of cols
	 * @param   searchable 	  boolean - if false then show also documents which are not marked as searchable
	 * @param string $condition
	 * @param unknown_type $calendar
	 * @param unknown_type $datefield
	 * @param unknown_type $date
	 * @param unknown_type $weekstart
	 * @param string $categoryids
	 * @return we_listview
	 */
	function __construct($name='0', $rows=999999999, $offset=0, $order='', $desc=false, $docType='', $cats='', $catOr=false, $casesensitive=false, $workspaceID='0', $contentTypes='', $cols='', $searchable=true, $condition='', $calendar='', $datefield='', $date='', $weekstart='', $categoryids='', $customerFilterType='off', $subfolders=true, $customers='', $id='', $languages='', $numorder=false, $hidedirindex = false){
		parent::__construct($name, $rows, $offset, $order, $desc, $cats, $catOr, $workspaceID, $cols, $calendar, $datefield, $date, $weekstart, $categoryids, $customerFilterType, $id);

		$this->docType = trim($docType);
		$this->casesensitive = $casesensitive;
		$this->contentTypes = $contentTypes;
		$this->searchable = $searchable;
		$this->subfolders = $subfolders;
		$this->customers = $customers;
		$this->customerArray = array();

		$calendar_select = '';
		$calendar_where = '';
		if($calendar != '')
			$this->fetchCalendar($condition, $calendar_select, $calendar_where);

		$this->defaultCondition = $condition;
		$this->condition = $condition;
		$this->condition = $this->condition ? $this->condition : (isset($GLOBALS['we_lv_condition']) ? $GLOBALS['we_lv_condition'] : '');

		$cond_where = ''; // #3763
		if($this->condition != ''){
			$condition_sql = $this->makeConditionSql($this->condition);
			if(!empty($condition_sql))
				$cond_where .= ' AND (' . $condition_sql . ')';
		}

		$this->languages = $languages;
		$this->languages = $this->languages ? $this->languages : (isset($GLOBALS['we_lv_languages']) ? $GLOBALS['we_lv_languages'] : '');

		if($this->languages != ''){
			$where_lang = ' AND (';
			$langArray = makeArrayFromCSV($this->languages);
			$where_lang .= FILE_TABLE . '.Language = "" ';
			for($i = 0; $i < count($langArray); $i++){
				$where_lang .= 'OR ' . FILE_TABLE . '.Language = "' . $langArray[$i] . '" ';
			}

			$where_lang .= ' ) ';
		} else{
			$where_lang = '';
		}

		if(stripos($this->order, ' desc') !== false){//was #3849
			$this->order = str_ireplace(' desc', '', $this->order);
			$this->desc = true;
		}

		$this->numorder = $numorder;
		$this->hidedirindex = $hidedirindex;
		$this->order = trim($this->order);

		switch($this->order){
			case 'we_id':
				$orderstring = ' ORDER BY ' . FILE_TABLE . '.ID ' . ($this->desc ? 'DESC' : '');
				break;
			case 'we_creationdate':
				$orderstring = ' ORDER BY ' . FILE_TABLE . '.CreationDate' . ($this->desc ? 'DESC' : '');
				break;
			case 'we_filename':
				$orderstring = ' ORDER BY ' . FILE_TABLE . '.Text' . ($this->desc ? 'DESC' : '');
				break;
			case 'we_moddate':
				$orderstring = ' ORDER BY ' . FILE_TABLE . '.ModDate' . ($this->desc ? 'DESC' : '');
				break;
			case 'we_published':
				$orderstring = ' ORDER BY ' . FILE_TABLE . '.Published' . ($this->desc ? 'DESC' : '');
				break;
			default:
				if($this->search){
					$orderstring = $this->order ?
						(' AND ' . LINK_TABLE . ".Name='" . $this->DB_WE->escape($this->order) . "' ORDER BY ranking," . CONTENT_TABLE . '.Dat' . ($this->desc ? ' DESC' : '')) :
						' ORDER BY ranking';
				} else{
					$orderstring = $this->order ?
						(' AND ' . LINK_TABLE . ".Name='" . $this->DB_WE->escape($this->order) . "' ORDER BY " . ($this->numorder ? '0+' : '') . CONTENT_TABLE . '.Dat' . ($this->desc ? ' DESC' : '')) :
						'';
				}
				break;
		}


		$sql_tail = getCatSQLTail($this->cats, FILE_TABLE, $this->catOr, $this->DB_WE, 'Category', true, $this->categoryids);

		$dt = ($this->docType) ? f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' WHERE DocType like "' . $this->DB_WE->escape($this->docType) . '"', 'ID', $this->DB_WE) : '#NODOCTYPE#';

		$ws_where = '';

		if($this->contentTypes){
			$this->contentTypes = str_replace('img', 'image/*', $this->contentTypes);
			$this->contentTypes = str_replace('wepage', 'text/webEdition', $this->contentTypes);
			$this->contentTypes = str_replace('binary', 'application/*', $this->contentTypes);
			$CtArr = makeArrayFromCSV($this->contentTypes);
			foreach($CtArr as $ct){
				$sql_tail .= ' AND ' . FILE_TABLE . '.ContentType = \'' . $this->DB_WE->escape($ct) . '\'';
			}
		}
		if($this->customerFilterType != 'off' && defined('CUSTOMER_FILTER_TABLE')){
			$sql_tail .= weDocumentCustomerFilter::getConditionForListviewQuery($this);
		}

		if($this->customers && $this->customers !== '*'){
			$custArr = makeArrayFromCSV($this->customers);

			$_wsql = ' ' . FILE_TABLE . '.WebUserID IN(' . $this->customers . ') ';

			foreach($custArr as $cid){

				$customerData = getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . intval($cid), $this->DB_WE);
				$this->customerArray['cid_' . $customerData['ID']] = $customerData;
			}

			$sql_tail .= ' AND (' . $_wsql . ') ';
		}

		$sql_tail .= $this->getIdQuery(FILE_TABLE . '.ID');

		if($this->search){

			if($this->workspaceID != ''){
				$workspaces = makeArrayFromCSV($this->workspaceID);
				$cond = array();
				foreach($workspaces as $id){
					$workspace = id_to_path($id, FILE_TABLE, $this->DB_WE);
					array_push($cond, '(' . INDEX_TABLE . ".Workspace like '$workspace/%' OR " . INDEX_TABLE . ".Workspace='" . $this->DB_WE->escape($workspace) . "')");
				}
				$ws_where = ' AND (' . implode(' OR ', $cond) . ')';
			}
			$bedingungen = preg_split('/ +/', $this->search);
			$searchfield = $this->casesensitive ? 'BText' : 'Text';

			$ranking = '0';
			$worte = array();
			$spalten = array(INDEX_TABLE . '.' . $searchfield);
			reset($bedingungen);
			while(list($k1, $v1) = each($bedingungen)) {
				if(preg_match('|^[-\+]|', $v1)){
					$not = (preg_match('^-', $v1)) ? 'NOT ' : '';
					$bed = preg_replace('/^[-\+]/', '', $v1);
					$klammer = array();
					reset($spalten);
					while(list($k, $v) = each($spalten)) {
						$klammer[] = sprintf("%s LIKE '%%%s%%'", $v, addslashes($bed));
					}
					if($not){
						$bedingungen3_sql[] = $not . '(' . join($klammer, ' OR ') . ')';
					} else{
						$bedingungen_sql[] = '(' . join($klammer, ' OR ') . ')';
					}
				} else{
					$klammer = array();
					reset($spalten);
					while(list($k, $v) = each($spalten)) {
						$klammer[] = sprintf("%s LIKE '%%%s%%'", $v, addslashes($v1));
					}
					$bed2 = '(' . join($klammer, ' OR ') . ')';
					$ranking .= '-' . $bed2;
					$bedingungen2_sql[] = $bed2;
				}
			}

			if(isset($bedingungen_sql) && count($bedingungen_sql) > 0){
				$bedingung_sql1 = ' ( ' . implode(' AND ', $bedingungen_sql) . (isset($bedingungen3_sql) && count($bedingungen3_sql) ? (' AND ' . implode(' AND ', $bedingungen3_sql)) : '') . ' ) ';
			} else if(isset($bedingungen2_sql) && count($bedingungen2_sql) > 0){
				$bedingung_sql2 = ' ( ( ' . implode(' OR ', $bedingungen2_sql) . (isset($bedingungen3_sql) && count($bedingungen3_sql) ? (' ) AND ' . implode(' AND ', $bedingungen3_sql)) : ' ) ') . ' ) ';
			} else if(isset($bedingungen3_sql) && count($bedingungen3_sql) > 0){
				$bedingung_sql2 = implode(' AND ', $bedingungen3_sql);
			}

			if(isset($bedingung_sql1) && isset($bedingung_sql2)){
				$bedingung_sql = ' ( ' . $bedingung1_sql . ' AND ' . $bedingung2_sql . ' ) ';
			} else if(isset($bedingung_sql1)){
				$bedingung_sql = $bedingung_sql1;
			} else{
				$bedingung_sql = $bedingung_sql2;
			}

			if($this->order == 'random()'){
				$q = 'SELECT ' . FILE_TABLE . '.ID as ID, ' . FILE_TABLE . ".WebUserID as WebUserID, RAND() as RANDOM $calendar_select FROM " . FILE_TABLE . "," . LINK_TABLE . "," . CONTENT_TABLE . "," . INDEX_TABLE . " WHERE " . ($this->searchable ? " " . FILE_TABLE . ".IsSearchable=1" : "1") . " $where_lang $cond_where $ws_where AND " . FILE_TABLE . ".IsFolder=0 AND " . LINK_TABLE . ".DID=" . FILE_TABLE . ".ID AND " . LINK_TABLE . ".CID=" . CONTENT_TABLE . ".ID AND " . FILE_TABLE . ".Published > 0 AND " . LINK_TABLE . ".DocumentTable='" . stripTblPrefix(FILE_TABLE) . "' AND " . INDEX_TABLE . ".DID=" . FILE_TABLE . ".ID AND $bedingung_sql" . (($dt != "#NODOCTYPE#") ? (" AND " . FILE_TABLE . '.DocType=' . intval($dt)) : '') . $sql_tail . $calendar_where . "GROUP BY ID ORDER BY RANDOM" . (($this->maxItemsPerPage > 0) ? (" limit " . abs($this->start) . "," . abs($this->maxItemsPerPage)) : "");
			} else{
				$q = 'SELECT distinct ' . FILE_TABLE . ".ID as ID, " . FILE_TABLE . ".WebUserID as WebUserID, $ranking as ranking $calendar_select FROM " . FILE_TABLE . "," . LINK_TABLE . "," . CONTENT_TABLE . "," . INDEX_TABLE . " WHERE " . ($this->searchable ? " " . FILE_TABLE . ".IsSearchable=1" : "1") . " $where_lang $cond_where $ws_where AND " . FILE_TABLE . ".IsFolder=0 AND " . LINK_TABLE . ".DID=" . FILE_TABLE . ".ID AND " . LINK_TABLE . ".CID=" . CONTENT_TABLE . ".ID AND " . FILE_TABLE . ".Published > 0 AND " . LINK_TABLE . ".DocumentTable='" . stripTblPrefix(FILE_TABLE) . "' AND " . INDEX_TABLE . ".DID=" . FILE_TABLE . ".ID AND $bedingung_sql" . (($dt != "#NODOCTYPE#") ? (" AND " . FILE_TABLE . '.DocType=' . intval($dt) ) : '') . $sql_tail . $calendar_where . " " . $orderstring . (($this->maxItemsPerPage > 0) ? (" limit " . abs($this->start) . "," . abs($this->maxItemsPerPage)) : "");
			}
		} else{

			if($this->workspaceID != ''){
				$workspaces = makeArrayFromCSV($this->workspaceID);
				$cond = array();
				if(!$this->subfolders){ // all entries with given parentIds
					$ws_where = ' AND (ParentID IN (' . implode(', ', $workspaces) . '))';
				} else{ // beneath the workspaceids
					foreach($workspaces as $id){
						$workspace = id_to_path($id, FILE_TABLE, $this->DB_WE);
						array_push($cond, "(" . FILE_TABLE . ".Path like '" . $this->DB_WE->escape($workspace) . "/%' OR " . FILE_TABLE . ".Path='" . $this->DB_WE->escape($workspace) . "')");
					}
					$ws_where = " AND (" . implode(" OR ", $cond) . ")";
				}
			}
			if($this->order == 'random()'){
				$q = 'SELECT ' . FILE_TABLE . '.ID as ID, ' . FILE_TABLE . ".WebUserID as WebUserID, RAND() as RANDOM $calendar_select FROM " . FILE_TABLE . "," . LINK_TABLE . "," . CONTENT_TABLE . " WHERE " . ($this->searchable ? " " . FILE_TABLE . ".IsSearchable=1" : "1") . " $where_lang $cond_where $ws_where AND " . FILE_TABLE . ".IsFolder=0 AND " . LINK_TABLE . ".DID=" . FILE_TABLE . ".ID AND " . LINK_TABLE . ".CID=" . CONTENT_TABLE . ".ID AND " . FILE_TABLE . ".Published > 0 AND " . LINK_TABLE . ".DocumentTable='" . stripTblPrefix(FILE_TABLE) . "'" . (($dt != "#NODOCTYPE#") ? (' AND ' . FILE_TABLE . '.DocType=' . intval($dt)) : '') . $sql_tail . $calendar_where . "GROUP BY ID ORDER BY RANDOM" . (($rows > 0) ? (" limit " . abs($this->start) . "," . abs($this->maxItemsPerPage)) : "");
			} else{
				$q = 'SELECT distinct ' . FILE_TABLE . '.ID as ID, ' . FILE_TABLE . ".WebUserID as WebUserID $calendar_select FROM " . FILE_TABLE . "," . LINK_TABLE . "," . CONTENT_TABLE . " WHERE " . ($this->searchable ? " " . FILE_TABLE . ".IsSearchable=1" : "1") . " $where_lang $cond_where $ws_where AND " . FILE_TABLE . ".IsFolder=0 AND " . LINK_TABLE . ".DID=" . FILE_TABLE . ".ID AND " . LINK_TABLE . ".CID=" . CONTENT_TABLE . ".ID AND " . FILE_TABLE . ".Published > 0 AND " . LINK_TABLE . ".DocumentTable='" . stripTblPrefix(FILE_TABLE) . "'" . (($dt != "#NODOCTYPE#") ? (' AND ' . FILE_TABLE . '.DocType=' . intval($dt)) : '') . $sql_tail . $calendar_where . " " . $orderstring . (($rows > 0) ? (" limit " . abs($this->start) . "," . abs($this->maxItemsPerPage)) : "");
			}
		}

		$this->DB_WE->query($q);
		$this->anz = $this->DB_WE->num_rows();

		if($this->customers === '*'){
			$_idListArray = array();
		}

		while($this->DB_WE->next_record()) {
			array_push($this->IDs, $this->DB_WE->f('ID'));
			if($calendar != ''){
				$this->calendar_struct['storage'][$this->DB_WE->f('ID')] = (int) $this->DB_WE->f('Calendar');
			}
			if($this->customers === '*' && intval($this->DB_WE->f('WebUserID')) > 0){
				$_idListArray[] = $this->DB_WE->f('WebUserID');
			}
		}
		if($this->customers === '*'){
			if(count($_idListArray) > 0){
				$_idListArray = array_unique($_idListArray);
				$_idlist = implode(',', $_idListArray);
				$db = new DB_WE();
				$db->query('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID IN(' . $_idlist . ')');
				while($db->next_record()) {
					$this->customerArray['cid_' . $db->f('ID')] = $db->Record;
				}
			}
			unset($_idListArray);
		}
		if($this->search){
			if($this->order == 'random()'){
				$q = 'SELECT ' . FILE_TABLE . '.ID as ID, ' . FILE_TABLE . '.WebUserID as WebUserID, RAND() as RANDOM FROM ' . FILE_TABLE . ',' . LINK_TABLE . ',' . CONTENT_TABLE . ',' . INDEX_TABLE . ' WHERE ' . ($this->searchable ? ' ' . FILE_TABLE . '.IsSearchable=1' : '1') . " $where_lang $cond_where $ws_where AND " . FILE_TABLE . ".IsFolder=0 AND " . LINK_TABLE . ".DID=" . FILE_TABLE . ".ID AND " . LINK_TABLE . ".CID=" . CONTENT_TABLE . ".ID AND " . FILE_TABLE . ".Published > 0 AND " . LINK_TABLE . ".DocumentTable='" . stripTblPrefix(FILE_TABLE) . "' AND " . INDEX_TABLE . ".DID=" . FILE_TABLE . ".ID AND $bedingung_sql" . (($dt != "#NODOCTYPE#") ? (" AND " . FILE_TABLE . '.DocType=' . intval($dt)) : '') . $sql_tail . $calendar_where . "GROUP BY ID ORDER BY RANDOM";
			} else{
				$q = 'SELECT DISTINCT ' . FILE_TABLE . '.ID as ID, ' . FILE_TABLE . ".WebUserID as WebUserID, $ranking as ranking FROM " . FILE_TABLE . "," . LINK_TABLE . "," . CONTENT_TABLE . "," . INDEX_TABLE . " WHERE " . ($this->searchable ? " " . FILE_TABLE . ".IsSearchable=1" : "1") . " $where_lang $cond_where $ws_where AND " . FILE_TABLE . ".IsFolder=0 AND " . LINK_TABLE . ".DID=" . FILE_TABLE . ".ID AND " . LINK_TABLE . ".CID=" . CONTENT_TABLE . ".ID AND " . FILE_TABLE . ".Published > 0 AND " . LINK_TABLE . ".DocumentTable='" . stripTblPrefix(FILE_TABLE) . "' AND " . INDEX_TABLE . ".DID=" . FILE_TABLE . ".ID AND $bedingung_sql" . (($dt != "#NODOCTYPE#") ? (" AND " . FILE_TABLE . '.DocType=' . intval($dt)) : '') . $sql_tail . $calendar_where . " " . $orderstring;
			}
		} else{
			if($this->order == 'random()'){
				$q = 'SELECT ' . FILE_TABLE . '.ID as ID, ' . FILE_TABLE . '.WebUserID as WebUserID, RAND() as RANDOM FROM ' . FILE_TABLE . ',' . LINK_TABLE . ',' . CONTENT_TABLE . ' WHERE ' . ($this->searchable ? ' ' . FILE_TABLE . '.IsSearchable=1' : '1') . " $where_lang $cond_where $ws_where AND " . FILE_TABLE . ".IsFolder=0 AND " . LINK_TABLE . ".DID=" . FILE_TABLE . ".ID AND " . LINK_TABLE . ".CID=" . CONTENT_TABLE . ".ID AND " . FILE_TABLE . ".Published > 0 AND " . LINK_TABLE . ".DocumentTable='" . stripTblPrefix(FILE_TABLE) . "'" . (($dt != "#NODOCTYPE#") ? (" AND " . FILE_TABLE . '.DocType=' . intval($dt)) : '') . $sql_tail . $calendar_where . "GROUP BY ID ORDER BY RANDOM";
			} else{
				$q = 'SELECT DISTINCT ' . FILE_TABLE . '.ID as ID, ' . FILE_TABLE . '.WebUserID as WebUserID FROM ' . FILE_TABLE . ',' . LINK_TABLE . ',' . CONTENT_TABLE . ' WHERE ' . ($this->searchable ? ' ' . FILE_TABLE . '.IsSearchable=1' : '1') . " $where_lang $cond_where $ws_where AND " . FILE_TABLE . ".IsFolder=0 AND " . LINK_TABLE . ".DID=" . FILE_TABLE . ".ID AND " . LINK_TABLE . ".CID=" . CONTENT_TABLE . ".ID AND " . FILE_TABLE . ".Published > 0 AND " . LINK_TABLE . ".DocumentTable='" . stripTblPrefix(FILE_TABLE) . "'" . (($dt != "#NODOCTYPE#") ? (" AND " . FILE_TABLE . '.DocType=' . intval($dt)) : '') . $sql_tail . $calendar_where . " " . $orderstring;
			}
		}

		$this->DB_WE->query($q);
		$this->anz_all = $this->DB_WE->num_rows();
		$this->adjustRows();
		if($calendar != '')
			$this->postFetchCalendar();
	}

	function next_record(){
		if($this->count < $this->anz){
			$count = $this->count;
			$fetch = false;
			if($this->calendar_struct['calendar'] != ''){
				listviewBase::next_record();
				$count = $this->calendar_struct['count'];
				$fetch = $this->calendar_struct['forceFetch'];
			}

			if($this->calendar_struct['calendar'] == '' || $fetch){
				$id = $this->IDs[$count];
				$this->DB_WE->query('SELECT ' . CONTENT_TABLE . '.BDID as BDID, ' . CONTENT_TABLE . '.Dat as Dat, ' . LINK_TABLE . '.Name as Name FROM ' . LINK_TABLE . ',' . CONTENT_TABLE . ' WHERE ' . LINK_TABLE . '.DID='.$id.' AND ' . LINK_TABLE . '.CID=' . CONTENT_TABLE . '.ID AND ' . LINK_TABLE . '.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"');
				$this->Record = array();
				while($this->DB_WE->next_record()) {
					if($this->DB_WE->f('BDID')){
						$this->Record[$this->DB_WE->f('Name')] = $this->DB_WE->f('BDID');
					} else{
						$this->Record[$this->DB_WE->f('Name')] = $this->DB_WE->f('Dat');
					}
				}
				$this->DB_WE->query('SELECT * FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id));
				if($this->DB_WE->next_record()){
					foreach($this->DB_WE->Record as $key => $val){
						$this->Record['wedoc_'.$key] = $val;
					}
				}

				$this->Record['WE_SHOPVARIANTS'] = 0;
				if(isset($this->Record['weInternVariantElement'])){
					$ShopVariants = @unserialize($this->Record['weInternVariantElement']);
					if(is_array($ShopVariants) && count($ShopVariants) > 0){
						$this->Record['WE_SHOPVARIANTS'] = count($ShopVariants);
					}
				}

				$this->Record['WE_PATH'] = $this->Record['wedoc_Path'];
				$this->Record['WE_TEXT'] = f('SELECT Text FROM ' . INDEX_TABLE . ' WHERE DID=' . intval($id), 'Text', $this->DB_WE);
				$this->Record['WE_ID'] = $id;

				if($this->customers && $this->Record['wedoc_WebUserID']){
					if(isset($this->customerArray['cid_' . $this->Record['wedoc_WebUserID']])){
						foreach($this->customerArray['cid_' . $this->Record['wedoc_WebUserID']] as $key => $value){
							$this->Record['WE_CUSTOMER_' . $key] = $value;
						}
					}
				}

				$this->count++;
			}

			return true;
		} else{
			$this->stop_next_row = $this->shouldPrintEndTR();
			if($this->cols && ($this->count <= $this->maxItemsPerPage) && !$this->stop_next_row){
				$this->Record = array();
				$this->DB_WE->Record = array();
				$this->DB_WE->Record['WE_PATH'] = '';
				$this->DB_WE->Record['WE_TEXT'] = '';
				$this->DB_WE->Record['WE_ID'] = '';
				$this->count++;
				return true;
			}
		}
		return false;
	}

	function f($key){
		return isset($this->Record[$key]) ? $this->Record[$key] : '';
	}

	function makeConditionSql($cond){

		$cond = str_replace('&gt;', '>', $cond);
		$cond = str_replace('&lt;', '<', $cond);

		$exparr = array();

		$arr = explode(' ', $cond);

		$logic = array();
		$logic['and'] = array();
		$logic['or'] = array();
		$logic['not'] = array();
		$logic['and'][0] = $arr[0];
		$current = 'and';
		$c = 0;
		for($i = 1; $i < count($arr); $i++){
			$elem = strtolower($arr[$i]);
			if(in_array($elem, array_keys($logic))){
				$c = count($logic[$current]);
				$current = $elem;
			} else{
				if(isset($logic[$current][$c]))
					$logic[$current][$c].=' ' . $arr[$i];
				else
					$logic[$current][$c] = $arr[$i];
			}
		}

		$sqlarr = '';
		foreach($logic as $oper => $arr){
			foreach($arr as $key => $exp){

				$match = array();
				$patterns = array('<>', '<=', '>=', '=', '<', '>', 'LIKE', 'IN');



				foreach($patterns as $pattern){


					$match = preg_split('/' . $pattern . '/', $exp, -1, PREG_SPLIT_NO_EMPTY);
					if(count($match) > 1){

						$match[0] = str_replace(array('(', ')', ' '), '', $match[0]); // #5719: einfache und OR-verknuepfte Conditions gefixt
						$match[1] = str_replace(array('(', ')', ' '), '', $match[1]); // #5719
						$sqlarr = (($sqlarr != '') ? $sqlarr . ' ' . strtoupper($oper) . ' ' : '') . $this->makeFieldCondition($match[0], $pattern, $match[1]);

						break;
					}
				}
			}
		}
		return $sqlarr;
	}

	function makeFieldCondition($name, $operation, $value){
		return '(' . LINK_TABLE . '.Name="' . $this->DB_WE->escape($name) . '" AND ' . CONTENT_TABLE . '.Dat ' . $operation . ' ' . $value . ')';
	}

}
