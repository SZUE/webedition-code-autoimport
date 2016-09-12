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
 * class    we_listview_object
 * @desc    class for tag <we:listview type="object">
 *
 */
class we_listview_object extends we_listview_objectBase{
	var $customerFilterType = false;
	var $customers = '';
	var $we_predefinedSQL = '';

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
	public function __construct($name, $rows, $offset, $order, $desc, $classID, $cats, $catOr, $condition, $triggerID, $cols, $seeMode, $searchable, $calendar, $datefield, $date, $weekstart, $categoryids, $workspaceID, $customerFilterType, $docID, $customers, $id, $we_predefinedSQL, $languages, $hidedirindex, $objectseourls){
		parent::__construct($name, $rows, $offset, $order, $desc, $cats, $catOr, $workspaceID, $cols, $calendar, $datefield, $date, $weekstart, $categoryids, $customerFilterType, $id);

		$this->classID = intval($classID);
		$this->triggerID = $triggerID;

		$this->seeMode = $seeMode; //	edit objects in seeMode
		$this->searchable = $searchable;
		$this->docID = $docID;
		$this->customers = $customers;
		$this->customerArray = [];

		$this->condition = $condition;
		$this->languages = $languages ? : (isset($GLOBALS["we_lv_languages"]) ? $GLOBALS["we_lv_languages"] : '');
		$this->objectseourls = $objectseourls;
		$this->hidedirindex = $hidedirindex;

		$where_lang = ($this->languages ?
				' AND of.Language IN ("' . implode('","', array_map('escape_sql_query', array_filter(array_map('trim', explode(',', $this->languages))))) . '")' :
				'');

		if($this->order && $this->desc && (!preg_match('|.+ desc$|i', $this->order))){
			$this->order .= ' DESC';
		}

		$this->we_predefinedSQL = $we_predefinedSQL;

		$this->Path = ($this->docID ?
				id_to_path($this->docID, FILE_TABLE, $this->DB_WE) :
				($this->triggerID && show_SeoLinks() ?
					id_to_path($this->triggerID, FILE_TABLE, $this->DB_WE) :
					(isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Path : '')
				)
			);


		// IMPORTANT for seeMode !!!! #5317
		$this->LastDocPath = (isset($_SESSION['weS']['last_webEdition_document']) ? $_SESSION['weS']['last_webEdition_document']['Path'] : '');

		$matrix = [];
		$this->fillMatrix($matrix, $this->classID, true);

		$calendar_select = '';
		$calendar_where = '';

		if($calendar){
			$this->fetchCalendar($this->condition, $calendar_select, $calendar_where, $matrix);
		}
		$sqlParts = $this->makeSQLParts($matrix, $this->classID, $this->order, $this->condition, true);
		//allways join the file table itself
		$sqlParts['tables'].=' JOIN ' . OBJECT_FILES_TABLE . ' of ON of.ID=ob' . $this->classID . '.OF_ID';

		$pid_tail = (isset($GLOBALS['we_doc']) ? we_objectFile::makePIDTail($GLOBALS['we_doc']->ParentID, $this->classID, $this->DB_WE, $GLOBALS['we_doc']->Table) : '');

		$cat_tail = ($this->cats || $this->categoryids ?
				we_category::getCatSQLTail($this->cats, 'of', $this->catOr, $this->DB_WE, 'Category', $this->categoryids) : '');

		$weDocumentCustomerFilter_tail = (defined('CUSTOMER_FILTER_TABLE') ?
				we_customer_documentFilter::getConditionForListviewQuery($this->customerFilterType, $this, $this->classID, $id) :
				'');

		$webUserID_tail = '';
		if($this->customers && $this->customers !== "*"){

			$wsql = ' of.WebUserID IN(' . $this->customers . ') ';
			$this->DB_WE->query('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID IN(' . $this->customers . ')');
			$encrypted = we_customer_customer::getEncryptedFields();
			while($this->DB_WE->next_record(MYSQL_ASSOC)){
				$this->customerArray['cid_' . $this->DB_WE->f('ID')] = array_merge($this->DB_WE->getRecord(), $encrypted);
			}

			$webUserID_tail = ' AND (' . $wsql . ') ';
		}

		if($sqlParts["tables"] || $we_predefinedSQL != ''){

			if($we_predefinedSQL){
				$this->DB_WE->query($we_predefinedSQL);
				$this->anz_all = $this->DB_WE->num_rows();
				$q = $we_predefinedSQL . (($this->maxItemsPerPage > 0) ? (' LIMIT ' . $this->start . ',' . $this->maxItemsPerPage) : '');
			} else {
				$idTail = $this->getIdQuery('ob' . $this->classID . '.OF_ID');

				if($this->workspaceID != ''){
					$workspaces = makeArrayFromCSV($this->workspaceID);
					$cond = [];
					foreach($workspaces as $wid){
						$workspace = id_to_path($wid, OBJECT_FILES_TABLE, $this->DB_WE);
						$cond[] = 'of.Path LIKE "' . $workspace . '/%"';
						$cond[] = 'of.Path="' . $workspace . '"';
					}
					$ws_tail = empty($cond) ? '' : ' AND (' . implode(' OR ', $cond) . ') ';
				} else {
					$ws_tail = '';
				}
				$this->DB_WE->query('SELECT of.ID ' . $calendar_select . ' FROM ' . $sqlParts['tables'] . ' WHERE ' . ($this->searchable ? ' of.IsSearchable=1 AND ' : '') . ($pid_tail ? $pid_tail . ' AND ' : '') . '1' . $where_lang . $cat_tail . ' ' . ($sqlParts['publ_cond'] ? (' AND ' . $sqlParts['publ_cond']) : '') . ' ' . ($sqlParts['cond'] ? ' AND (' . $sqlParts['cond'] . ') ' : '') . $calendar_where . $ws_tail . $weDocumentCustomerFilter_tail . $webUserID_tail . $idTail . $sqlParts['groupBy']);
				$this->anz_all = $this->DB_WE->num_rows();
				if($calendar){
					while($this->DB_WE->next_record(MYSQL_ASSOC)){
						$this->IDs[] = $this->DB_WE->f('ID');
						$this->calendar_struct['storage'][$this->DB_WE->f('ID')] = (int) $this->DB_WE->f('Calendar');
					}
				}
				$q = 'SELECT ' . $sqlParts['fields'] . $calendar_select . ' FROM ' . $sqlParts['tables'] . ' WHERE ' . ($this->searchable ? ' of.IsSearchable=1 AND ' : '') . ($pid_tail ? $pid_tail.' AND ' : '') . '1' . $where_lang . $cat_tail . ' ' . ($sqlParts['publ_cond'] ? (' AND ' . $sqlParts['publ_cond']) : '') . ' ' . ($sqlParts['cond'] ? ' AND (' . $sqlParts['cond'] . ') ' : '') . $calendar_where . $ws_tail . $weDocumentCustomerFilter_tail . $webUserID_tail . $idTail . $sqlParts['groupBy'] . $sqlParts["order"] . (($this->maxItemsPerPage > 0) ? (' LIMIT ' . $this->start . ',' . $this->maxItemsPerPage) : '');
			}
			$this->DB_WE->query($q);
			$this->anz = $this->DB_WE->num_rows();

			if($this->customers === '*'){
				$idListArray = [];
				while($this->DB_WE->next_record(MYSQL_ASSOC)){
					if(intval($this->DB_WE->f(self::PROPPREFIX . 'WebUserID')) > 0){
						$idListArray[] = $this->DB_WE->f(self::PROPPREFIX . 'WebUserID');
					}
				}
				if($idListArray){
					$idlist = implode(',', array_unique($idListArray));
					$db = new DB_WE();
					$db->query('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID IN(' . $idlist . ')');
					$encrypted = we_customer_customer::getEncryptedFields();
					while($db->next_record(MYSQL_ASSOC)){
						$this->customerArray['cid_' . $db->f('ID')] = array_merge($db->Record, $encrypted);
					}
				}
				unset($idListArray);

				$this->DB_WE->seek(0);
			}
		} else {
			$this->anz_all = 0;
			$this->anz = 0;
		}
		if($calendar != ''){
			$this->postFetchCalendar();
		}
	}

	function next_record(){
		$count = $this->count;
		$fetch = false;
		if($this->calendar_struct['calendar']){
			if($this->count >= $this->anz){
				return false;
			}
			parent::next_record();
			$count = $this->calendar_struct['count'];
			$fetch = $this->calendar_struct['forceFetch'];
			$this->DB_WE->Record = [];
			if(!$fetch){
				return !empty($this->calendar_struct['calendar']);
			}
		}

		if($this->DB_WE->next_record(MYSQL_ASSOC)){
			$paramName = $this->docID ? 'we_oid' : 'we_objectID';
			$this->DB_WE->Record[self::PROPPREFIX . 'PATH'] = $this->Path . '?' . $paramName . '=' . $this->DB_WE->Record['OF_ID'];
			$this->DB_WE->Record[self::PROPPREFIX . 'CUSTOMER_ID'] = $this->DB_WE->Record[self::PROPPREFIX . 'WEBUSERID'];
			$this->DB_WE->Record[self::PROPPREFIX . 'TRIGGERID'] = ($this->triggerID ? : intval($this->DB_WE->f(self::PROPPREFIX . 'TRIGGERID')));
			$this->DB_WE->Record[self::PROPPREFIX . 'URL'] = $this->DB_WE->f(self::PROPPREFIX . 'URL');
			$this->DB_WE->Record[self::PROPPREFIX . 'TEXT'] = $this->DB_WE->f(self::PROPPREFIX . 'TEXT');
			$this->DB_WE->Record[self::PROPPREFIX . 'ID'] = $this->DB_WE->f(self::PROPPREFIX . 'ID');
			$this->DB_WE->Record[self::PROPPREFIX . 'SHOPVARIANTS'] = 0; //check this for global variants

			$path_parts = pathinfo($this->Path);
			if($this->objectseourls && $this->DB_WE->Record[self::PROPPREFIX . 'URL'] && show_SeoLinks()){
				if(!$this->triggerID && $this->DB_WE->Record[self::PROPPREFIX . 'TRIGGERID']){
					$path_parts = pathinfo(id_to_path($this->DB_WE->f(self::PROPPREFIX . 'TRIGGERID')));
				}
				$this->DB_WE->Record[self::PROPPREFIX . 'PATH'] = (!empty($path_parts['dirname']) && $path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' .
					($this->hidedirindex && seoIndexHide($path_parts['basename']) ?
						'' :
						$path_parts['filename'] . '/'
					) . $this->DB_WE->Record[self::PROPPREFIX . 'URL'];
			} else {
				$this->DB_WE->Record[self::PROPPREFIX . 'PATH'] = ($this->hidedirindex && seoIndexHide($path_parts['basename']) ?
						($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' :
						$this->Path
					) . '?' . $paramName . '=' . $this->DB_WE->Record['OF_ID'];
			}
			if(($dat = $this->f(we_base_constants::WE_VARIANTS_ELEMENT_NAME))){
				$variants = we_unserialize($dat);
				if(is_array($variants) && !empty($variants)){
					$this->DB_WE->Record[self::PROPPREFIX . 'SHOPVARIANTS'] = count($variants);
				}
			}
			// for seeMode #5317
			$this->DB_WE->Record[self::PROPPREFIX . 'LASTPATH'] = $this->LastDocPath . '?' . $paramName . '=' . $this->DB_WE->Record['OF_ID'];
			if($this->customers && $this->DB_WE->Record[self::PROPPREFIX . 'WEBUSERID']){
				if(isset($this->customerArray['cid_' . $this->DB_WE->Record[self::PROPPREFIX . 'WEBUSERID']])){
					foreach($this->customerArray['cid_' . $this->DB_WE->Record[self::PROPPREFIX . 'WEBUSERID']] as $key => $value){
						$this->DB_WE->Record[self::PROPPREFIX . 'CUSTOMER_' . $key] = $value;
					}
				}
			}

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

		return !empty($this->calendar_struct['calendar']);
	}

}
