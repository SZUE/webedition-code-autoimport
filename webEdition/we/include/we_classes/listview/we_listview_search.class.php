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
 * class
 * @desc    class for tag <we:listview type='search'>
 *          the difference to the normal listview is, that you can only
 *          display the fields from the index table (tblIndex) which are
 *          Title, Description we_text, we_path
 *
 */
class we_listview_search extends we_listview_base{

	var $docType = ''; /* doctype string */
	var $class = 0; /* ID of a class. Search only in Objects of this class */
	var $triggerID = 0; /* ID of a document which to use for displaying thr detail page */
	var $casesensitive = false; /* set to true when a search should be case sensitive */
	var $ClassName = __CLASS__;
	var $languages = ''; //string of Languages, separated by ,
	var $objectseourls = false;
	var $hidedirindex = false;

	/**
	 *
	 * @desc    constructor of class
	 *
	 * @param   name         string - name of listview
	 * @param   rows          integer - number of rows to display per page
	 * @param   offset        integer - start offset of first page
	 * @param   order         string - field name(s) to order by
	 * @param   desc          boolean - set to true, if order should be descendend
	 * @param   docType       string - doctype
	 * @param   class         integer - ID of a class. Search only in Objects of this class
	 * @param   cats          string - comma separated categories
	 * @param   catOr         boolean - set to true if it should be an 'OR condition'
	 * @param   casesensitive boolean - set to true when a search should be case sensitive
	 * @param   workspaceID   string - commaseperated list of id's of workspace
	 * @param   cols   		  integer - to display a table this is the number of cols
	 *
	 */
	function __construct($name, $rows, $offset, $order, $desc, $docType, $class, $cats, $catOr, $casesensitive, $workspaceID, $triggerID, $cols, $customerFilterType, $languages, $hidedirindex, $objectseourls){
		parent::__construct($name, $rows, $offset, $order, $desc, $cats, $catOr, $workspaceID, $cols, '', '', '', '', '', $customerFilterType);

		$this->triggerID = $triggerID;
		$this->objectseourls = $objectseourls;
		$this->hidedirindex = $hidedirindex;
		$this->languages = $languages ? : (isset($GLOBALS['we_lv_languages']) ? $GLOBALS['we_lv_languages'] : '');

		$where_lang = ($this->languages ? ' AND Language IN ("' . implode('","', makeArrayFromCSV($this->languages)) . '") ' : '');

		// correct order
		$orderArr = array();
		$random = false;

		switch($this->order? : '__noorder'){
			case '__noorder':
				break;
			case 'we_id':
			case 'we_creationdate':
			case 'we_filename':
				$ord = str_replace('we_id', 'ID' . ($this->desc ? ' DESC' : ''), $this->order);
				//$ord = str_replace("we_creationdate",FILE_TABLE . ".CreationDate",$ord); // NOTE: this won't work, cause Indextable doesn't know this field & filetable is not used in this query
				$ord = str_replace('we_creationdate', '', $ord);
				$this->order = str_replace('we_filename', 'Path', $ord);
				break;
			default:
				$orderArr1 = array_map('trim', explode(',', $this->order));
				if(in_array('random()', $orderArr1)){
					$random = true;
					break;
				}
				foreach($orderArr1 as $o){
					if(trim($o)){
						$foo = preg_split('/ +/', $o);
						$oname = $foo[0];
						$otype = isset($foo[1]) ? $foo[1] : '';
						$orderArr[] = array('oname' => $oname, 'otype' => $otype);
					}
				}
				$this->order = '';
				foreach($orderArr as $o){
					switch($o['oname']){
						case 'OID':
						case 'DID':
						case 'ID':
							$this->order .= 'ID' . ((trim(strtolower($o['otype'])) === 'desc') ? ' DESC' : '') . ',';
							break;
						case 'Title':
						case 'Path':
						case 'Text':
						case 'Workspace':
						case 'Description':
							$this->order .= $o['oname'] . ((trim(strtolower($o['otype'])) === 'desc') ? ' DESC' : '') . ',';
					}
				}
				$this->order = rtrim($this->order, ',');
		}


		if($this->order && $this->desc && (!preg_match('|.+ desc$|i', $this->order))){
			$this->order .= ' DESC';
		}

		$this->docType = trim($docType);
		$this->class = intval($class);
		$this->casesensitive = $casesensitive;
		$this->search = $this->DB_WE->escape($this->search);

		$cat_tail = ($this->cats ? we_category::getCatSQLTail($this->cats, INDEX_TABLE, $this->catOr, $this->DB_WE) : '');
		$dt = ($this->docType ? f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' WHERE DocType LIKE "' . $this->DB_WE->escape($this->docType) . '"', '', $this->DB_WE) : 0);

		if($dt && $this->class){
			$dtcl_query = ' AND (Doctype="' . $this->DB_WE->escape($dt) . '" OR ClassID=' . $this->class . ') ';
		} else if($dt){
			$dtcl_query = ' AND Doctype="' . $this->DB_WE->escape($dt) . '" ';
		} else if($this->class){
			$dtcl_query = ' AND ClassID=' . $this->class . ' ';
		} else {
			$dtcl_query = '';
		}

		//FIXME: use fulltext index: MATCH(Text) AGAINST([+-]words* IN BINARY MODE)

		$spalte = ($this->casesensitive ? 'BINARY ' : '') . 'Text';
		$bOR = $bAND = array();
		foreach(preg_split('/ +/', $this->search) as $v1){
			if(preg_match('|^[-\+]|', $v1)){
				$bAND[] = (preg_match('|^-|', $v1) ? 'NOT ' : '') .
						$spalte . ' LIKE "%' . preg_replace('|^[-\+]|', '', $v1) . '%"';
			} else {
				$bOR[] = $spalte . ' LIKE "%' . $v1 . '%"';
			}
		}

		if($bOR){
			$bAND[] = '(' . implode(' OR ', $bOR) . ')';
		}

		$bedingung_sql = '(' . implode(' AND ', $bAND) . ')';
		$ranking = '(ROUND(MATCH(Text) AGAINST("' . str_replace(array('+', '-'), '', $this->search) . '"),3))';

		if($this->workspaceID){
			$workspaces = id_to_path(explode(',', $this->workspaceID), FILE_TABLE, $this->DB_WE, false, true);
			$cond = array();
			foreach($workspaces as $workspace){
				$cond[] = '(Workspace LIKE "' . $this->DB_WE->escape($workspace) . '/%" OR Workspace="' . $this->DB_WE->escape($workspace) . '")';
			}
			$ws_where = ' AND (' . implode(' OR ', $cond) . ')';
		} else {
			$ws_where = '';
		}

		$weDocumentCustomerFilter_tail = (defined('CUSTOMER_FILTER_TABLE') ?
						we_customer_documentFilter::getConditionForListviewQuery($this->customerFilterType, $this->ClassName) :
						'');

		$where = ' WHERE ' . $bedingung_sql . ' ' . $dtcl_query . ' ' . $cat_tail . ' ' . $ws_where . ' ' . $where_lang . ' ' . $weDocumentCustomerFilter_tail;
		$this->DB_WE->query('SELECT 1 FROM ' . INDEX_TABLE . $where);
		$this->anz_all = $this->DB_WE->num_rows();

		$this->DB_WE->query(
				'SELECT Category,ID,ID AS DID,ID AS OID,ClassID,Text,Workspace,WorkspaceID,Title,Description,Path,Language, ' . ($random ? 'RAND() ' : $ranking) . ' AS ranking ' .
				'FROM ' . INDEX_TABLE .
				$where . ' ORDER BY ranking DESC ' . ($this->order ? (',' . $this->order) : '') . (($this->maxItemsPerPage > 0) ? (' LIMIT ' . intval($this->start) . ',' . intval($this->maxItemsPerPage)) : ''));
		$this->anz = $this->DB_WE->num_rows();
	}

	public function next_record(){
		if($this->DB_WE->next_record()){
			if($this->DB_WE->Record['ClassID'] && $this->objectseourls && show_SeoLinks()){
				$objectdaten = getHash('SELECT Url,TriggerID FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($this->DB_WE->Record['ID']) . ' LIMIT 1');
				$objecttriggerid = ($this->triggerID ? : ($objectdaten ? $objectdaten['TriggerID'] : 0));

				$path_parts = ($objecttriggerid ?
								pathinfo(id_to_path($objecttriggerid)) :
								pathinfo($_SERVER['SCRIPT_NAME'])
						);

				$pidstr = ($this->DB_WE->Record['WorkspaceID'] ? '?pid=' . intval($this->DB_WE->Record['WorkspaceID']) : '');

				if(NAVIGATION_DIRECTORYINDEX_NAMES && $this->hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
					$this->DB_WE->Record['WE_PATH'] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') .
							($objectdaten['Url'] ?
									'/' . $objectdaten['Url'] . $pidstr :
									'/?we_objectID=' . $this->DB_WE->Record['ID'] . str_replace('?', '&amp;', $pidstr));
				} else {
					$this->DB_WE->Record['WE_PATH'] = ($objectdaten && $objectdaten['Url'] ?
									($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . $path_parts['filename'] . '/' . $objectdaten['Url'] . $pidstr :
									$_SERVER['SCRIPT_NAME'] . '?we_objectID=' . $this->DB_WE->Record['ID'] . str_replace('?', '&amp;', $pidstr));
				}
				$this->DB_WE->Record['wedoc_Path'] = $this->DB_WE->Record['WE_PATH'];
				$this->DB_WE->Record['we_WE_URL'] = $objectdaten ? $objectdaten['Url'] : '';
				$this->DB_WE->Record['we_WE_TRIGGERID'] = $objecttriggerid;
			} else {
				$this->DB_WE->Record['wedoc_Path'] = $this->DB_WE->Record['Path'];
				$this->DB_WE->Record['WE_PATH'] = $this->DB_WE->Record['Path'];
			}
			$this->DB_WE->Record['WE_LANGUAGE'] = $this->DB_WE->Record['Language'];
			$this->DB_WE->Record['WE_TEXT'] = $this->DB_WE->Record['Text'];
			$this->DB_WE->Record['wedoc_Category'] = $this->DB_WE->Record['Category'];
			$this->DB_WE->Record['WE_ID'] = $this->DB_WE->Record['ID'];
			$this->count++;
			return true;
		}
		$this->stop_next_row = $this->shouldPrintEndTR();
		if($this->cols && ($this->count <= $this->maxItemsPerPage) && !$this->stop_next_row){
			$this->DB_WE->Record = array(
				'WE_LANGUAGE' => '',
				'WE_PATH' => '',
				'WE_TEXT' => '',
				'WE_ID' => '',
			);
			$this->count++;
			return true;
		}

		return false;
	}

	function f($key){
		return $this->DB_WE->f($key);
	}

}
