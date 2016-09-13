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
 * class    we_listview_multiobject
 * @desc    class for tag <we:listview type="multiobject">
 *
 */
//FIXME: is this class not ~ listview_object? why is this not the base class???
class we_listview_multiobject extends we_listview_objectBase{
	var $objects = ''; /* Comma sepearated list of all objetcs to show in this listview */

	/**
	 * @desc    constructor of class
	 *
	 * @param   name          string - name of listview
	 * @param   rows          integer - number of rows to display per page
	 * @param   offset        integer - start offset of first page
	 * @param   order         string - field name(s) to order by
	 * @param   desc          boolean - set to true, if order should be descendend
	 * @param   cats          string - comma separated categories
	 * @param   catOr         boolean - set to true if it should be an "OR condition"
	 * @param   condition     string - condition string (like SQL)
	 * @param   triggerID     integer - ID of a document which to use for displaying the detail page
	 * @param   cols          ??
	 * @param   seeMode       boolean - value if objects shall be accessible in seeMode (default true)
	 * @param   searchable 	  boolean - if false then show also documents which are not marked as searchable
	 * @param	unknown_type  $calendar
	 * @param	unknown_type  $datefield
	 * @param	unknown_type  $date
	 * @param	unknown_type  $weekstart
	 * @param	string        $categoryids
	 *
	 */
	public function __construct($name, $rows = 9999999, $offset = 0, $order = '', $desc = false, $cats = '', $catOr = '', $condition = '', $triggerID = '', $cols = '', $seeMode = true, $searchable = true, $calendar = '', $datefield = '', $date = '', $weekstart = '', $categoryids = '', $customerFilterType = 'false', $docID = 0, $languages = '', $hidedirindex = false, $objectseourls = false){

		parent::__construct($name, $rows, $offset, $order, $desc, $cats, $catOr, 0, $cols, $calendar, $datefield, $date, $weekstart, $categoryids, $customerFilterType);

		$data = 0;
		if(!empty($GLOBALS['we_lv_array']) && ($parent_lv = end($GLOBALS['we_lv_array']))){
			if(($dat = $parent_lv->f($name))){
				$data = we_unserialize($dat);
			}
		} elseif(!empty($GLOBALS['lv'])){
			if(($dat = $GLOBALS['lv']->f($name))){
				$data = we_unserialize($dat);
			}
		} elseif($GLOBALS['we_doc']->getElement($name)){
			$data = we_unserialize($GLOBALS['we_doc']->getElement($name));
		}

		$objects = isset($data['objects']) ? $data['objects'] : $data;
		if(empty($objects)){
			return;
		}
		$this->objects = $objects;
		//FIXME: can we use defaultsArray?!
		$this->classID = f('SELECT TableID FROM ' . OBJECT_FILES_TABLE . ' WHERE ID IN (' . implode(',', $objects) . ') LIMIT 1');
		if(!$this->classID){
			$this->Record = [];
			$this->anz_all = 0;
			return;
		}
		$this->triggerID = $triggerID;
		$this->condition = $condition;
		$this->searchable = $searchable;
		$this->docID = $docID; //Bug #3720

		$this->condition = $this->condition;
		$this->languages = $languages ? : (isset($GLOBALS['we_lv_languages']) ? $GLOBALS['we_lv_languages'] : '');
		$this->objectseourls = $objectseourls;
		$this->hidedirindex = $hidedirindex;

		if($this->desc && (!preg_match('|.+ desc$|i', $this->order))){
			$this->order .= ' DESC';
		}

		$this->Path = ($this->triggerID && show_SeoLinks() ?
				id_to_path($this->triggerID, FILE_TABLE, $this->DB_WE) :
				(isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Path : '')
			);


		// IMPORTANT for seeMode !!!! #5317
		$this->LastDocPath = (isset($_SESSION['weS']['last_webEdition_document']) ? $_SESSION['weS']['last_webEdition_document']['Path'] : '');

		$matrix = [];
		$this->fillMatrix($matrix, $this->classID);
		$calendar_select = '';
		$calendar_where = '';

		if($calendar){
			$this->fetchCalendar($this->condition, $calendar_select, $calendar_where, $matrix);
		}
		$sqlParts = $this->makeSQLParts($matrix, $this->classID, $this->order, $this->condition, false);

		$weDocumentCustomerFilter_tail = (defined('CUSTOMER_FILTER_TABLE') ? we_customer_documentFilter::getConditionForListviewQuery($this->customerFilterType, $this, $this->classID) : '');

		if($sqlParts['tables']){
			$where = implode(' AND ', array_filter([
					'ids' => 'of.ID IN (' . implode(',', $this->objects) . ')',
					'search' => ($this->searchable ? 'of.IsSearchable=1' : ''),
					'lang' => ($this->languages ? 'of.Language IN ("' . implode('","', array_map('escape_sql_query', array_filter(array_map('trim', explode(',', $this->languages))))) . '")' : ''),
					'cat' => ($this->cats || $this->categoryids ? we_category::getCatSQLTail($this->cats, 'of', $this->catOr, $this->DB_WE, 'Category', $this->categoryids) : ''),
					'publ' => $sqlParts['publ_cond'],
					'cond' => ($sqlParts['cond'] ? '(' . $sqlParts['cond'] . ')' : ''),
					'cal' => $calendar_where,
				])) . $weDocumentCustomerFilter_tail . $sqlParts['groupBy'];

			$this->DB_WE->query('SELECT of.ID ' . $calendar_select . ' FROM ' . $sqlParts['tables'] . ' JOIN ' . OBJECT_FILES_TABLE . ' of ON of.ID=ob' . $this->classID . '.OF_ID WHERE ' . $where);
			$mapping = []; // KEY = ID -> VALUE = ROWID
			$i = 0;
			while($this->DB_WE->next_record(MYSQL_ASSOC)){
				$mapping[$this->DB_WE->Record['ID']] = $i++;
				$this->IDs[] = $this->DB_WE->f('ID');
				if($calendar){
					$this->calendar_struct["storage"][$this->DB_WE->f('ID')] = intval($this->DB_WE->f('Calendar'));
				}
			}

			if($this->order){
				$this->anz_all = 0;
				$count = array_count_values($this->objects);
				foreach($mapping as $objid => $rowid){
					if(isset($count[$objid])){
						for($i = 0; $i < $count[$objid]; $i++){
							$this->anz_all++;
						}
					}
				}
			} else {
				$this->anz_all = count($this->objects);
			}

			$this->DB_WE->query('SELECT ' . $sqlParts['fields'] . $calendar_select . ' FROM ' . $sqlParts['tables'] . ' JOIN ' . OBJECT_FILES_TABLE . ' of ON of.ID=ob' . $this->classID . '.OF_ID WHERE ' . $where . $sqlParts['order'] . (($rows > 0 && $this->order) ? (' LIMIT ' . $this->start . ',' . $this->rows) : ''));

			$mapping = []; // KEY = ID -> VALUE = ROWID
			$i = 0;
			while($this->DB_WE->next_record(MYSQL_ASSOC)){
				$mapping[$this->DB_WE->Record['OF_ID']] = $i++;
			}

			if($this->order){
				$count = array_count_values($this->objects);
				foreach($mapping as $objid => $rowid){
					for($i = 0; $i < $count[$objid]; $i++){
						$this->Record[] = $rowid;
					}
				}
			} else {
				for($i = $offset; $i < min($offset + $rows, count($this->objects)); $i++){
					if(in_array($this->objects[$i], array_keys($mapping))){
						$this->Record[] = $mapping[$this->objects[$i]];
					}
				}
			}
			$this->anz = count($this->Record);
		} else {
			$this->anz_all = 0;
			$this->anz = 0;
		}
		if($calendar != ''){
			$this->postFetchCalendar();
		}
	}

	function next_record(){
		if($this->calendar_struct['calendar']){
			if($this->count >= $this->anz){
				return false;
			}
			parent::next_record();
			$fetch = $this->calendar_struct['forceFetch'];
			$this->DB_WE->Record = [];
		} else {
			$fetch = false;
		}

		if(!$this->calendar_struct['calendar'] || $fetch){

			if($this->count < count($this->Record)){
				$this->DB_WE->Record($this->Record[$this->count]);
				$this->DB_WE->Record[self::PROPPREFIX . 'PATH'] = $this->Path . '?we_objectID=' . $this->DB_WE->Record['OF_ID'];
				$path_parts = pathinfo($this->Path);
				if($this->objectseourls && $this->DB_WE->Record[self::PROPPREFIX . 'URL'] && show_SeoLinks()){
					if(!$this->triggerID && $this->DB_WE->Record[self::PROPPREFIX . 'TRIGGERID'] != 0){
						$path_parts = pathinfo(id_to_path($this->DB_WE->f(self::PROPPREFIX . 'TRIGGERID')));
					}
					if($this->hidedirindex && seoIndexHide($path_parts['basename'])){
						$this->DB_WE->Record[self::PROPPREFIX . 'PATH'] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . $this->DB_WE->Record[self::PROPPREFIX . 'URL'];
					} else {
						$this->DB_WE->Record[self::PROPPREFIX . 'PATH'] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . $path_parts['filename'] . '/' . $this->DB_WE->Record[self::PROPPREFIX . 'URL'];
					}
				} elseif($this->hidedirindex && seoIndexHide($path_parts['basename'])){
					$this->DB_WE->Record[self::PROPPREFIX . 'PATH'] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/?we_objectID=' . $this->DB_WE->Record[self::PROPPREFIX . 'ID'];
				} else {
					$this->DB_WE->Record[self::PROPPREFIX . 'PATH'] = $this->Path . '?we_objectID=' . $this->DB_WE->Record[self::PROPPREFIX . 'ID'];
				}
				$this->DB_WE->Record[self::PROPPREFIX . 'TRIGGERID'] = ($this->triggerID ? : intval($this->DB_WE->f(self::PROPPREFIX . 'TRIGGERID')));
				$this->DB_WE->Record[self::PROPPREFIX . 'URL'] = $this->DB_WE->f(self::PROPPREFIX . 'URL');
				$this->DB_WE->Record[self::PROPPREFIX . 'TEXT'] = $this->DB_WE->f(self::PROPPREFIX . 'TEXT');
				$this->DB_WE->Record[self::PROPPREFIX . 'ID'] = $this->DB_WE->f('OF_ID');

				// for seeMode #5317
				$this->DB_WE->Record[self::PROPPREFIX . 'LASTPATH'] = $this->LastDocPath . '?we_objectID=' . $this->DB_WE->Record["OF_ID"];
				$this->count++;
				return true;
			}
			$this->stop_next_row = $this->shouldPrintEndTR();
			if($this->cols && ($this->count <= $this->maxItemsPerPage) && !$this->stop_next_row){
				$this->DB_WE->Record = array(
					'WE_PATH' => '',
					'WE_TEXT' => '',
					'WE_ID' => '',
				);
				$this->count++;
				return true;
			}
			return false;
		}

		return ($this->calendar_struct['calendar'] != '');
	}

}
