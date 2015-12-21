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
 * class    we_object_listview
 * @desc    class for tag <we:listview type="object">
 *
 */
class we_object_listview extends we_listview_base{

	var $classID; /* ID of a class */
	var $triggerID; /* ID of a document which to use for displaying thr detail page */
	var $condition = ''; /* condition string (like SQL) */
	var $ClassName = __CLASS__;
	var $Path; /* internal: Path of document which to use for displaying thr detail page */
	var $IDs = array();
	var $searchable = true;
	var $customerFilterType = false;
	var $customers = '';
	var $we_predefinedSQL = '';
	var $languages = ''; //string of Languages, separated by ,
	var $objectseourls = false;
	var $hidedirindex = false;

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
	function __construct($name, $rows, $offset, $order, $desc, $classID, $cats, $catOr, $condition, $triggerID, $cols, $seeMode, $searchable, $calendar, $datefield, $date, $weekstart, $categoryids, $workspaceID, $customerFilterType, $docID, $customers, $id, $we_predefinedSQL, $languages, $hidedirindex, $objectseourls){
		parent::__construct($name, $rows, $offset, $order, $desc, $cats, $catOr, $workspaceID, $cols, $calendar, $datefield, $date, $weekstart, $categoryids, $customerFilterType, $id);

		$this->classID = $classID;
		$this->triggerID = $triggerID;

		$this->seeMode = $seeMode; //	edit objects in seeMode
		$this->searchable = $searchable;
		$this->docID = $docID;
		$this->customers = $customers;
		$this->customerArray = array();

		$this->condition = $condition ? : (isset($GLOBALS["we_lv_condition"]) ? $GLOBALS["we_lv_condition"] : '');
		$this->languages = $languages ? : (isset($GLOBALS["we_lv_languages"]) ? $GLOBALS["we_lv_languages"] : '');
		$this->objectseourls = $objectseourls;
		$this->hidedirindex = $hidedirindex;

		$_obxTable = OBJECT_X_TABLE . intval($this->classID);

		$where_lang = ($this->languages ?
						' AND ' . $_obxTable . '.OF_Language IN ("' . implode('","', explode(',', $this->languages)) . '")' :
						'');

		if($this->desc && (!preg_match('|.+ desc$|i', $this->order))){
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

		$matrix = array();
		$join = $this->fillMatrix($matrix, $this->classID, $this->DB_WE);

		$calendar_select = '';
		$calendar_where = '';

		if($calendar != ''){
			$this->fetchCalendar($this->condition, $calendar_select, $calendar_where, $matrix);
		}
		$sqlParts = $this->makeSQLParts($matrix, $this->classID, $this->order, $this->condition);
		//allways join the file table itself
		$sqlParts['tables'].=' JOIN `' . OBJECT_FILES_TABLE . '` ON `' . OBJECT_FILES_TABLE . '`.ID=`' . OBJECT_X_TABLE . $this->classID . '`.OF_ID';

		$pid_tail = (isset($GLOBALS['we_doc']) ? makePIDTail($GLOBALS['we_doc']->ParentID, $this->classID, $this->DB_WE, $GLOBALS['we_doc']->Table) : '1');

		$cat_tail = ($this->cats || $this->categoryids ? we_category::getCatSQLTail($this->cats, $_obxTable, $this->catOr, $this->DB_WE, "OF_Category", $this->categoryids) : '');

		$weDocumentCustomerFilter_tail = (defined('CUSTOMER_FILTER_TABLE') ?
						we_customer_documentFilter::getConditionForListviewQuery($this->customerFilterType, $this->ClassName, $this->classID, $id) :
						'');

		$webUserID_tail = '';
		if($this->customers && $this->customers !== "*"){

			$_wsql = ' ' . $_obxTable . '.OF_WebUserID IN(' . $this->customers . ') ';
			$this->DB_WE->query('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID IN(' . $this->customers . ')');
			while($this->DB_WE->next_record()){
				$this->customerArray['cid_' . $this->DB_WE->f('ID')] = $this->DB_WE->getRecord();
			}

			$webUserID_tail = ' AND (' . $_wsql . ') ';
		}

		if($sqlParts["tables"] || $we_predefinedSQL != ''){

			if($we_predefinedSQL){
				$this->DB_WE->query($we_predefinedSQL);
				$this->anz_all = $this->DB_WE->num_rows();
				$q = $we_predefinedSQL . (($this->maxItemsPerPage > 0) ? (' LIMIT ' . $this->start . ',' . $this->maxItemsPerPage) : '');
			} else {
				$_idTail = $this->getIdQuery($_obxTable . '.OF_ID');

				if($this->workspaceID != ''){
					$workspaces = makeArrayFromCSV($this->workspaceID);
					$cond = array();
					foreach($workspaces as $wid){
						$workspace = id_to_path($wid, OBJECT_FILES_TABLE, $this->DB_WE);
						$cond[] = $_obxTable . '.OF_Path LIKE "' . $workspace . '/%"';
						$cond[] = $_obxTable . '.OF_Path="' . $workspace . '"';
					}
					$ws_tail = empty($cond) ? '' : ' AND (' . implode(' OR ', $cond) . ') ';
				} else {
					$ws_tail = '';
				}
				$this->DB_WE->query('SELECT ' . $_obxTable . '.OF_ID AS ID ' . $calendar_select . ' FROM ' . $sqlParts["tables"] . ' WHERE ' . ($this->searchable ? " " . $_obxTable . '.OF_IsSearchable=1 AND' : "") . " " . $pid_tail . ' AND ' . $_obxTable . '.OF_ID!=0 ' . $where_lang . ($join ? " AND ($join) " : "") . $cat_tail . " " . ($sqlParts["publ_cond"] ? (" AND " . $sqlParts["publ_cond"]) : "") . " " . ($sqlParts["cond"] ? ' AND (' . $sqlParts['cond'] . ') ' : '') . $calendar_where . $ws_tail . $weDocumentCustomerFilter_tail . $webUserID_tail . $_idTail . $sqlParts['groupBy']);
				$this->anz_all = $this->DB_WE->num_rows();
				if($calendar != ""){
					while($this->DB_WE->next_record()){
						$this->IDs[] = $this->DB_WE->f('ID');
						if($calendar != ''){
							$this->calendar_struct["storage"][$this->DB_WE->f("ID")] = (int) $this->DB_WE->f("Calendar");
						}
					}
				}
				$q = 'SELECT ' . $sqlParts["fields"] . $calendar_select . ' FROM ' . $sqlParts['tables'] . ' WHERE ' . ($this->searchable ? ' ' . $_obxTable . '.OF_IsSearchable=1 AND' : '') . ' ' . $pid_tail . ' AND ' . $_obxTable . '.OF_ID!=0 ' . $where_lang . ($join ? " AND ($join) " : "") . $cat_tail . " " . ($sqlParts["publ_cond"] ? (' AND ' . $sqlParts["publ_cond"]) : '') . ' ' . ($sqlParts["cond"] ? ' AND (' . $sqlParts['cond'] . ') ' : '') . $calendar_where . $ws_tail . $weDocumentCustomerFilter_tail . $webUserID_tail . $_idTail . $sqlParts['groupBy'] . $sqlParts["order"] . (($this->maxItemsPerPage > 0) ? (' LIMIT ' . $this->start . ',' . $this->maxItemsPerPage) : '');
			}
			$this->DB_WE->query($q);
			$this->anz = $this->DB_WE->num_rows();

			if($this->customers === '*'){
				$_idListArray = array();
				while($this->DB_WE->next_record()){
					if(intval($this->DB_WE->f("OF_WebUserID")) > 0){
						$_idListArray[] = $this->DB_WE->f("OF_WebUserID");
					}
				}
				if($_idListArray){
					$_idlist = implode(',', array_unique($_idListArray));
					$db = new DB_WE();
					$db->query('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID IN(' . $_idlist . ')');
					while($db->next_record()){
						$this->customerArray['cid_' . $db->f('ID')] = $db->Record;
					}
				}
				unset($_idListArray);

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

	function tableInMatrix($matrix, $table){
		if(OBJECT_X_TABLE . $this->classID == $table){
			return true;
		}
		foreach($matrix as $foo){
			if($foo["table"] == $table){
				return true;
			}
		}
		return false;
	}

	function fillMatrix(&$matrix, $classID, we_database_base $db = null){
		$db = ($db ? : new DB_WE());
		$table = OBJECT_X_TABLE . intval($classID);
		$joinWhere = array();
		$regs = array();
		$tableInfo = we_objectFile::getSortedTableInfo($classID, true, $db, true);
		foreach($tableInfo as $fieldInfo){
			if(preg_match('/(.+?)_(.*)/', $fieldInfo['name'], $regs)){
				$type = $regs[1];
				$name = $regs[2];
				if($type === 'object' && $name != $this->classID){
					if(!isset($matrix['we_object_' . $name]['type']) || !$matrix['we_object_' . $name]['type']){
						$matrix['we_object_' . $name]['type'] = $type;
						$matrix['we_object_' . $name]['table'] = $table;
						$matrix['we_object_' . $name]['table2'] = OBJECT_X_TABLE . $name;
						$matrix['we_object_' . $name]['classID'] = $classID;
						$foo = $this->fillMatrix($matrix, $name, $db);
						$joinWhere[] = OBJECT_X_TABLE . intval($classID) . '.' . we_object::QUERY_PREFIX . $name . '=' . OBJECT_X_TABLE . $name . '.OF_ID';
						if($foo){
							$joinWhere[] = $foo;
						}
					}
				} else {
					if(!isset($matrix[$name])){
						$matrix[$name]['type'] = $type;
						$matrix[$name]['table'] = $table;
						$matrix[$name]['classID'] = $classID;
						$matrix[$name]['table2'] = $table;
					}
				}
			}
		}
		return implode(' AND ', $joinWhere);
	}

	public static function encodeEregString(array $match){
		$in = $match[1];
		$out = '';
		for($i = 0; $i < strlen($in); $i++){
			$out .= '&' . ord(substr($in, $i, 1)) . ';';
		}
		return "'" . $out . "'";
	}

	public static function decodeEregString(array $match){
		return "'" . preg_replace_callback("/&([^;]+);/", function (array $match){
					return chr($match[1]);
				}, $match[1]) . "'";
	}

	function makeSQLParts($matrix, $classID, $order, $cond){
		//FIXME: order ist totaler nonsense - das geht deutlich einfacher
		$from = $orderArr = $descArr = $ordertmp = array();

		$cond = str_replace(array('&gt;', '&lt;'), array('>', '<',), $cond);

		$cond = ' ' . preg_replace_callback("/'([^']*)'/", 'we_object_listview::encodeEregString', $cond) . ' ';


		if($order && ($order != 'random()')){
			$foo = makeArrayFromCSV($order);
			foreach($foo as $f){
				$g = explode(' ', trim($f));
				$orderArr[] = $g[0];
				$descArr[] = intval(isset($g[1]) && strtolower(trim($g[1])) === 'desc');
			}
		}

		//get Metadata for class (default title, etc.)
		//BugFix #4629
		$_fieldnames = getHash('SELECT DefaultDesc,DefaultTitle,DefaultKeywords,CreationDate,ModDate FROM ' . OBJECT_TABLE . ' WHERE ID=' . $classID, $this->DB_WE);
		$_selFields = '';
		$classID = intval($classID);
		foreach($_fieldnames as $_key => $_val){
			if(!$_val || $_val === '_'){ // bug #4657
				continue;
			}
			if(!is_numeric($_key) && $_val){
				switch($_key){
					case 'DefaultDesc':
						$_selFields .= '`' . OBJECT_X_TABLE . $classID . '`.`' . $_val . '` AS we_Description,';
						break;
					case 'DefaultTitle':
						$_selFields .= '`' . OBJECT_X_TABLE . $classID . '`.`' . $_val . '` AS we_Title,';
						break;
					case 'DefaultKeywords':
						$_selFields .= '`' . OBJECT_X_TABLE . $classID . '`.`' . $_val . '` AS we_Keywords,';
						break;
				}
			}
		}
		$f = '`' . OBJECT_X_TABLE . $classID . '`.OF_ID AS ID,`' . OBJECT_X_TABLE . $classID . '`.OF_Templates,`' . OBJECT_X_TABLE . $classID . '`.OF_ID,`' . OBJECT_X_TABLE . $classID . '`.OF_Category,`' . OBJECT_X_TABLE . $classID . '`.OF_Text,`' . OBJECT_X_TABLE . $classID . '`.OF_Url,`' . OBJECT_X_TABLE . $classID . '`.OF_TriggerID,`' . OBJECT_X_TABLE . $classID . '`.OF_WebUserID,`' . OBJECT_X_TABLE . $classID . '`.OF_Language,`' . OBJECT_X_TABLE . $classID . '`.`OF_Published`' . ' AS we_wedoc_Published,' . $_selFields;
		foreach($matrix as $n => $p){
			$n2 = $n;
			if(strpos($n, 'we_object_') === 0){
				$n = substr($n, 10);
			}
			$f .= '`' . $p['table'] . '`.`' . $p['type'] . '_' . $n . '` AS `we_' . $n2 . '`,';
			$from[] = $p['table'];
			$from[] = $p['table2'];
			if(in_array($n, $orderArr)){
				$pos = array_search($n, $orderArr);
				$ordertmp[$pos] = '`' . $p['table'] . '`.`' . $p['type'] . '_' . $n . '`' . ($descArr[$pos] ? ' DESC' : '');
			}
			$cond = preg_replace("/([\!\=%&\(\*\+\.\/<>|~ ])$n([\!\=%&\)\*\+\.\/<>|~ ])/", "$1" . $p['table'] . ".`" . $p['type'] . '_' . $n . "`$2", $cond);
		}

		$cond = preg_replace_callback("/'([^']*)'/", 'we_object_listview::decodeEregString', $cond);

		ksort($ordertmp);
		$_tmporder = trim(str_ireplace('desc', '', $order));
		switch($_tmporder){
			case 'we_id':
			case 'we_filename':
			case 'we_published':
			case 'we_moddate':
				$_tmporder = strtr($_tmporder, array(
					'we_id' => '`' . OBJECT_X_TABLE . $classID . '`.OF_ID',
					'we_filename' => '`' . OBJECT_X_TABLE . $classID . '`.OF_Text',
					'we_published' => '`' . OBJECT_X_TABLE . $classID . '`.OF_Published',
					'we_moddate' => '`' . OBJECT_FILES_TABLE . '`.ModDate',
				));
				$order = ' ORDER BY ' . $_tmporder . ($this->desc ? ' DESC' : '');
				break;
			case 'random()':
				$order = ' ORDER BY RANDOM ';
				break;
			default:
				$order = makeCSVFromArray($ordertmp);
				if($order){
					$order = ' ORDER BY ' . $order;
				}
				break;
		}

		$tb = array_unique($from);

		$publ_cond = array();
		foreach($tb as &$t){
			$t = '`' . $t . '`';
			$publ_cond[] = '(' . $t . '.OF_Published>0 OR ' . $t . '.OF_ID=0)';
		}

		return array(//FIXME: maybe random can be changed by time%ID or sth. which is faster and quite rand enough
			'fields' => rtrim($f, ',') . ($order === ' ORDER BY RANDOM ' ? ', RAND() AS RANDOM ' : ''),
			'order' => $order,
			'tables' => implode(' JOIN ', $tb),
			'groupBy' => (count($tb) > 1) ? ' GROUP BY `' . OBJECT_X_TABLE . $classID . '`.OF_ID ' : '',
			'publ_cond' => $publ_cond ? ' ( ' . implode(' AND ', $publ_cond) . ' ) ' : '',
			'cond' => trim($cond)
		);
	}

	function next_record(){
		$count = $this->count;
		$fetch = false;
		if($this->calendar_struct['calendar']){
			if($this->count < $this->anz){
				we_listview_base::next_record();
				$count = $this->calendar_struct['count'];
				$fetch = $this->calendar_struct['forceFetch'];
				$this->DB_WE->Record = array();
			} else {
				return false;
			}
		}

		if(!$this->calendar_struct['calendar'] || $fetch){
			$ret = $this->DB_WE->next_record();

			if($ret){
				$tmp = getHash('SELECT * FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . $this->DB_WE->f('OF_ID'), null, MYSQL_ASSOC);
				foreach($tmp as $key => $val){
					$this->DB_WE->Record['we_wedoc_' . $key] = $val;
				}

				$paramName = $this->docID ? 'we_oid' : 'we_objectID';
				$this->DB_WE->Record['we_wedoc_Path'] = $this->Path . '?' . $paramName . '=' . $this->DB_WE->Record['OF_ID'];
				$this->DB_WE->Record['we_wedoc_WebUserID'] = isset($this->DB_WE->Record['OF_WebUserID']) ? $this->DB_WE->Record['OF_WebUserID'] : 0; // needed for ifRegisteredUserCanChange tag
				$this->DB_WE->Record['we_WE_CUSTOMER_ID'] = $this->DB_WE->Record['we_wedoc_WebUserID'];
				$path_parts = pathinfo($this->Path);
				if($this->objectseourls && $this->DB_WE->Record['OF_Url'] != '' && show_SeoLinks()){
					if(!$this->triggerID && $this->DB_WE->Record['OF_TriggerID'] != 0){
						$path_parts = pathinfo(id_to_path($this->DB_WE->f('OF_TriggerID')));
					}
					$this->DB_WE->Record['we_WE_PATH'] = ($path_parts && $path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' .
							(show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES && $this->hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))) ?
									'' :
									$path_parts['filename'] . '/' ) .
							$this->DB_WE->Record['OF_Url'];
				} else {
					if(show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES && $this->hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
						$this->DB_WE->Record['we_WE_PATH'] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . '?' . $paramName . '=' . $this->DB_WE->Record['OF_ID'];
					} else {
						$this->DB_WE->Record['we_WE_PATH'] = $this->Path . '?' . $paramName . '=' . $this->DB_WE->Record['OF_ID'];
					}
				}
				$this->DB_WE->Record['we_WE_TRIGGERID'] = ($this->triggerID ? : intval($this->DB_WE->f('OF_TriggerID')));
				$this->DB_WE->Record['we_WE_URL'] = $this->DB_WE->f('OF_Url');
				$this->DB_WE->Record['we_WE_TEXT'] = $this->DB_WE->f('OF_Text');
				$this->DB_WE->Record['we_WE_ID'] = $this->DB_WE->f('OF_ID');
				$this->DB_WE->Record['we_wedoc_Category'] = $this->DB_WE->f('OF_Category');
				$this->DB_WE->Record['we_WE_SHOPVARIANTS'] = 0;
				if(defined('WE_SHOP_VARIANTS_ELEMENT_NAME') && isset($this->DB_WE->Record['we_' . WE_SHOP_VARIANTS_ELEMENT_NAME])){
					$ShopVariants = @unserialize($this->DB_WE->Record['we_' . WE_SHOP_VARIANTS_ELEMENT_NAME]);
					if(is_array($ShopVariants) && count($ShopVariants) > 0){
						$this->DB_WE->Record['we_WE_SHOPVARIANTS'] = count($ShopVariants);
					}
				}
				// for seeMode #5317
				$this->DB_WE->Record['we_wedoc_lastPath'] = $this->LastDocPath . '?' . $paramName . '=' . $this->DB_WE->Record['OF_ID'];
				if($this->customers && $this->DB_WE->Record['we_wedoc_WebUserID']){
					if(isset($this->customerArray['cid_' . $this->DB_WE->Record['we_wedoc_WebUserID']])){
						foreach($this->customerArray['cid_' . $this->DB_WE->Record['we_wedoc_WebUserID']] as $key => $value){
							$this->DB_WE->Record['we_WE_CUSTOMER_' . $key] = $value;
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
		}

		return ($this->calendar_struct["calendar"] != '');
	}

	function f($key){
		return $this->DB_WE->f('we_' . $key);
	}

}
