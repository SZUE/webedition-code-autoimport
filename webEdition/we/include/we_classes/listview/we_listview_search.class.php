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

		$where_lang = ($this->languages ? ' AND i.Language IN ("' . implode('","', array_map('escape_sql_query', array_filter(array_map('trim', explode(',', $this->languages))))) . '") ' : '');

		// correct order
		$orderArr = [];

		$orderArr1 = array_map('trim', explode(',', $this->order));
		$random = (in_array('random()', $orderArr1));
		$orderArr1 = $random ? [] : $orderArr1;

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
				case 'we_creationdate':
					$this->order .= 'COALESCE(f.CreationDate' . (defined('OBJECT_FILES_TABLE') ? ',of.CreationDate' : '') . ')' . ((trim(strtolower($o['otype'])) === 'desc') ? ' DESC' : '') . ',';
					break;
				case 'we_moddate':
					$this->order .='COALESCE(f.ModDate' . (defined('OBJECT_FILES_TABLE') ? ',of.ModDate' : '') . ')' . ((trim(strtolower($o['otype'])) === 'desc') ? ' DESC' : '') . ',';
					break;
				case 'we_filename':
					$this->order .= 'COALESCE(f.Text' . (defined('OBJECT_FILES_TABLE') ? ',of.Text' : '') . ')' . ((trim(strtolower($o['otype'])) === 'desc') ? ' DESC' : '') . ',';
					break;
				case 'Path':
					$this->order .= 'COALESCE(f.Path' . (defined('OBJECT_FILES_TABLE') ? ',of.Path' : '') . ')' . ((trim(strtolower($o['otype'])) === 'desc') ? ' DESC' : '') . ',';
					break;
				case 'Workspace':
					$this->order .= 'wsp.Path' . ((trim(strtolower($o['otype'])) === 'desc') ? ' DESC' : '') . ',';
					break;
				case 'we_id':
				case 'OID':
				case 'DID':
				case 'ID':
					$this->order .= 'ID' . ((trim(strtolower($o['otype'])) === 'desc') ? ' DESC' : '') . ',';
					break;
				case 'Title':
				case 'Text':
				case 'Description':
					$this->order .= $o['oname'] . ((trim(strtolower($o['otype'])) === 'desc') ? ' DESC' : '') . ',';
					break;
			}
		}
		$this->order = rtrim($this->order, ',');

		/* if($this->order && $this->desc && (!preg_match('|.+ desc$|i', $this->order))){
		  $this->order .= ' DESC';
		  } */

		$this->docType = trim($docType);
		$this->class = intval($class);
		$this->casesensitive = $casesensitive;
		$this->search = $this->DB_WE->escape($this->search);

		$cat_tail = ($this->cats ? ' AND '.we_category::getCatSQLTail($this->cats, 'i', $this->catOr, $this->DB_WE) : '');
		$dt = ($this->docType ? f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' WHERE DocType LIKE "' . $this->DB_WE->escape($this->docType) . '"', '', $this->DB_WE) : 0);

		if($dt && $this->class){
			$dtcl_query = ' AND (i.Doctype="' . $this->DB_WE->escape($dt) . '" OR i.ClassID=' . $this->class . ') ';
		} else if($dt){
			$dtcl_query = ' AND i.Doctype="' . $this->DB_WE->escape($dt) . '" ';
		} else if($this->class){
			$dtcl_query = ' AND i.ClassID=' . $this->class . ' ';
		} else {
			$dtcl_query = '';
		}

		//FIXME: use fulltext index: MATCH(Text) AGAINST([+-]words* IN BINARY MODE)

		$spalte = ($this->casesensitive ? 'BINARY ' : '') . 'i.Text';
		$bOR = $bAND = [];
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
		$ranking = '(ROUND(MATCH(i.Text) AGAINST("' . str_replace(array('+', '-'), '', $this->search) . '"),3))';
		$ws_where = '';
		if($this->workspaceID){
			$ids = array_filter(explode(',', $this->workspaceID));
			if($ids){
				$workspaces = id_to_path($ids, FILE_TABLE, $this->DB_WE, true);
				$cond = array('i.WorkspaceID IN (' . implode(',', $ids) . ')');
				foreach($workspaces as $workspace){
					$cond[] = 'wsp.Path LIKE "' . $this->DB_WE->escape($workspace) . '/%"';
				}
				$ws_where = ' AND (' . implode(' OR ', $cond) . ')';
			}
		}
		$weDocumentCustomerFilter_tail = (defined('CUSTOMER_FILTER_TABLE') ?
				we_customer_documentFilter::getConditionForListviewQuery($this->customerFilterType, $this) :
				'');

		$where = ' WHERE ' . $bedingung_sql . ' ' . $dtcl_query . ' ' . $cat_tail . ' ' . $ws_where . ' ' . $where_lang . ' ' . $weDocumentCustomerFilter_tail;
		$this->anz_all = f('SELECT COUNT(DISTINCT i.ID,i.WorkspaceID) FROM ' . INDEX_TABLE . ' i LEFT JOIN ' . FILE_TABLE . ' wsp ON wsp.ID=i.WorkspaceID ' . $where, '', $this->DB_WE);

		$this->DB_WE->query(
			'SELECT i.Category,i.ID,i.ID AS DID,i.ID AS OID,i.ClassID,i.Text,COALESCE(wsp.Path,"/") AS Workspace,i.WorkspaceID,i.Title,i.Description,COALESCE(f.Path' . (defined('OBJECT_FILES_TABLE') ? ',of.Path' : '') . ') AS Path,i.Language, ' . ($random ? 'RAND() ' : $ranking) . ' AS ranking ' .
			'FROM ' . INDEX_TABLE . ' i LEFT JOIN ' . FILE_TABLE . ' wsp ON wsp.ID=i.WorkspaceID LEFT JOIN ' . FILE_TABLE . ' f ON (i.ID=f.ID AND i.ClassID=0) ' .
			(defined('OBJECT_FILES_TABLE') ? 'LEFT JOIN ' . OBJECT_FILES_TABLE . ' of ON (i.ID=of.ID AND i.ClassID>0) ' : '') .
			$where .
			' GROUP BY i.ClassID,i.ID ORDER BY ranking DESC ' . ($this->order ? (',' . $this->order) : '') . (($this->maxItemsPerPage > 0) ? (' LIMIT ' . intval($this->start) . ',' . intval($this->maxItemsPerPage)) : ''));
		$this->anz = $this->DB_WE->num_rows();
	}

	public function next_record(){
		if($this->DB_WE->next_record(MYSQL_ASSOC)){
			$fileData = getHash('SELECT * FROM ' .
				($this->DB_WE->Record['ClassID'] ? OBJECT_FILES_TABLE : FILE_TABLE ) .
				' WHERE ID=' . intval($this->DB_WE->Record['ID']) . ' LIMIT 1');
			foreach($fileData as $key => $val){
				$this->DB_WE->Record[self::PROPPREFIX . strtoupper($key)] = $val;
			}
			if($this->DB_WE->Record['ClassID'] && $this->objectseourls && show_SeoLinks()){
				$objecttriggerid = ($this->triggerID ? : ($fileData ? $fileData['TriggerID'] : 0));

				$path_parts = ($objecttriggerid ?
						pathinfo(id_to_path($objecttriggerid)) :
						pathinfo($_SERVER['SCRIPT_NAME'])
					);

				$pidstr = ($this->DB_WE->Record['WorkspaceID'] ? '?pid=' . intval($this->DB_WE->Record['WorkspaceID']) : '');

				if($this->hidedirindex && seoIndexHide($path_parts['basename'])){
					$this->DB_WE->Record[self::PROPPREFIX . 'PATH'] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') .
						($fileData['Url'] ?
							'/' . $fileData['Url'] . $pidstr :
							'/?we_objectID=' . $this->DB_WE->Record['ID'] . str_replace('?', '&amp;', $pidstr));
				} else {
					$this->DB_WE->Record[self::PROPPREFIX . 'PATH'] = ($fileData && $fileData['Url'] ?
							($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . $path_parts['filename'] . '/' . $fileData['Url'] . $pidstr :
							$_SERVER['SCRIPT_NAME'] . '?we_objectID=' . $this->DB_WE->Record['ID'] . str_replace('?', '&amp;', $pidstr));
				}
				$this->DB_WE->Record[self::PROPPREFIX . 'TRIGGERID'] = $objecttriggerid;
			}
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
		$repl = 0;
		$key = preg_replace('/^(OF|wedoc|we)_/i', '', $key, $repl);
		if($repl){
			$key = strtoupper($key);
		}

		return $this->DB_WE->f($key);
	}

	public function getCustomerRestrictionQuery($specificCustomersQuery, $classID, $mfilter, $listQuery){
		return 'FROM ' . CUSTOMER_FILTER_TABLE . ' cf WHERE cf.modelType!="folder" AND ' . $mfilter . ' AND (' . $listQuery . ' OR ' . $specificCustomersQuery . ')';
	}

}
