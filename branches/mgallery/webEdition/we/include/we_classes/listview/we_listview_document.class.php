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
 * @desc    class for tag <we:listview>
 *
 */
class we_listview_document extends we_listview_base{
	var $docType = ''; /* doctype string */
	var $IDs = array(); /* array of ids with pages which are found */
	var $casesensitive = false; /* set to true when a search should be case sensitive */
	var $contentTypes = '';
	var $searchable = true;
	var $condition = ''; /* condition string (like SQL) */
	var $subfolders = true; // regard subfolders
	var $customers = '';
	var $languages = ''; //string of Languages, separated by ,
	var $numorder = false; // #3846
	public $triggerID = 0;
	protected $joins = array();
	protected $orderWhere = array();
	protected $table = FILE_TABLE;
	protected $group = '';

	/**
	 *
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
	 * @return we_listview_document
	 */
	function __construct($name, $rows, $offset, $order, $desc, $docType, $cats, $catOr, $casesensitive, $workspaceID, $contentTypes, $cols, $searchable, $condition, $calendar, $datefield, $date, $weekstart, $categoryids, $customerFilterType, $subfolders, $customers, $id, $languages, $numorder, $hidedirindex, $triggerID){
		parent::__construct($name, $rows, $offset, $order, $desc, $cats, $catOr, $workspaceID, $cols, $calendar, $datefield, $date, $weekstart, $categoryids, $customerFilterType, $id);
		$this->docType = trim($docType);
		$this->casesensitive = $casesensitive;
		$this->contentTypes = $contentTypes;
		$this->searchable = $searchable;
		$this->subfolders = $subfolders;
		$this->customers = $customers;
		$this->customerArray = array();
		if($this->table == VFILE_TABLE){
			$id = $this->id = 0;
		}

		$this->group.=($this->group ? ',' : '') . FILE_TABLE . '.ID';

		$calendar_select = $calendar_where = '';

		if($calendar != ''){
			$this->fetchCalendar($condition, $calendar_select, $calendar_where);
		}

		$this->condition = $condition;

		$cond_where = // #3763
			($this->condition != '' && ($condition_sql = $this->makeConditionSql($this->condition)) ?
				' AND (' . $condition_sql . ')' :
				'');

		$this->languages = $languages ? : (isset($GLOBALS['we_lv_languages']) ? $GLOBALS['we_lv_languages'] : '');
		$langArray = $this->languages ? array_filter(array_map('trim', explode(',', $this->languages))) : '';

		$where_lang = ($langArray ?
				' AND ' . FILE_TABLE . '.Language IN("","' . implode('","', array_map('escape_sql_query', $langArray)) . '") ' :
				'');

		if(stripos($this->order, ' desc') !== false){//was #3849
			$this->order = str_ireplace(' desc', '', $this->order);
			$this->desc = true;
		}

		$this->numorder = $numorder;
		$this->hidedirindex = $hidedirindex;
		$this->order = trim($this->order);
		$this->triggerID = $triggerID;
		$random = false;

		$order = array();
		$tmpOrder = explode(',', $this->order);
		foreach($tmpOrder as $ord){
			switch(trim($ord)){
				case 'we_id':
					$order[] = FILE_TABLE . '.ID' . ($this->desc ? ' DESC' : '');
					break;
				case 'we_creationdate':
					$order[] = FILE_TABLE . '.CreationDate' . ($this->desc ? ' DESC' : '');
					break;
				case 'we_path':
					$order[] = FILE_TABLE . '.Path' . ($this->desc ? ' DESC' : '');
					break;
				case 'we_filename':
					$order[] = FILE_TABLE . '.Text' . ($this->desc ? ' DESC' : '');
					break;
				case 'we_moddate':
					$order[] = FILE_TABLE . '.ModDate' . ($this->desc ? ' DESC' : '');
					break;
				case 'we_published':
					$order[] = FILE_TABLE . '.Published' . ($this->desc ? ' DESC' : '');
					break;
				case 'random()':
					$random = true;
					$order[] = 'RANDOM';
				case '':
					break;
				case 'VFILE':
					$order[] = 'fl.position';
					break;
				default:
					$cnt = count($this->joins);
					$this->joins[] = ' LEFT JOIN ' . LINK_TABLE . ' ll' . $cnt . ' ON ll' . $cnt . '.DID=' . FILE_TABLE . '.ID LEFT JOIN ' . CONTENT_TABLE . ' cc' . $cnt . ' ON ll' . $cnt . '.CID=cc' . $cnt . '.ID';
					$this->orderWhere[] = 'll' . $cnt . '.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '" AND ll' . $cnt . '.nHash=x\'' . md5($ord) . '\'';
					if($this->search){
						$order[] = 'ranking';
					}
					$order[] = ($this->numorder ? '0+' : '') . 'cc' . $cnt . '.Dat' . ($this->desc ? ' DESC' : '');
					break;
			}
		}
		$orderstring = $order ? ' ORDER BY ' . implode(',', $order) : '';
		$joinstring = implode('', $this->joins);
		$orderwhereString = implode(' AND ', $this->orderWhere) . ($this->orderWhere ? ' AND ' : '');

		$sql_tail = ($this->cats || $this->categoryids ? we_category::getCatSQLTail($this->cats, FILE_TABLE, $this->catOr, $this->DB_WE, 'Category', $this->categoryids) : '');

		$dt = ($this->docType ? f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' WHERE DocType LIKE "' . $this->DB_WE->escape($this->docType) . '"', '', $this->DB_WE) : -1);

		$ws_where = '';

		if($this->contentTypes){
			$this->contentTypes = str_replace(array('img', 'wepage', 'binary'), array(we_base_ContentTypes::IMAGE, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::APPLICATION), $this->contentTypes);
			$CtArr = explode(',', $this->contentTypes);
			if($CtArr){
				$sql_tail .= ' AND ' . FILE_TABLE . '.ContentType IN ("' . implode('","', array_map('escape_sql_query', $CtArr)) . '")';
			}
		}
		if(defined('CUSTOMER_FILTER_TABLE')){
			$sql_tail .= we_customer_documentFilter::getConditionForListviewQuery($this->customerFilterType, $this, 0, $id);
		}

		if($this->customers && $this->customers !== '*'){
			foreach(explode(',', $this->customers) as $cid){
				$customerData = array_merge(getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . intval($cid), $this->DB_WE), we_customer_customer::getEncryptedFields());
				$this->customerArray['cid_' . $customerData['ID']] = $customerData;
			}

			$sql_tail .= ' AND (' . FILE_TABLE . '.WebUserID IN(' . $this->customers . ')) ';
		}

		$sql_tail .= $this->getIdQuery(FILE_TABLE . '.ID');

		if($this->search){
			if($this->workspaceID){
				$cond = array(
					'i.WorkspaceID IN(' . implode(',', $this->workspaceID) . ')'
				);
				$workspaces = array_map('escape_sql_query', id_to_path(explode(',', $this->workspaceID), FILE_TABLE, $this->DB_WE));

				foreach($workspaces as $workspace){
					$cond[] = 'wsp.Path LIKE "' . $workspace . '/%"';
				}
				$ws_where = ' AND (' . implode(' OR ', $cond) . ')';
			}
			$bedingungen = preg_split('/ +/', $this->search);

			$ranking = '0';
			$spalten = array(($this->casesensitive ? 'BINARY ' : '') . 'i.Text');

			foreach($bedingungen as $v1){
				if(preg_match('|^[-\+]|', $v1)){
					$not = (preg_match('^-', $v1));
					$bed = preg_replace('/^[-\+]/', '', $v1);
					$klammer = array();
					reset($spalten);
					foreach($spalten as $v){
						$klammer[] = sprintf('%s LIKE "%%%s%%"', $v, addslashes($bed));
					}
					if($not){
						$bedingungen_sql[] = ' NOT (' . implode(' OR ', $klammer) . ')';
					} else {
						$bedingungen_sql[] = '(' . implode(' OR ', $klammer) . ')';
					}
				} else {
					$klammer = array();
					foreach($spalten as $v){
						$klammer[] = sprintf('%s LIKE "%%%s%%"', $v, addslashes($v1));
					}
					$bed2 = '(' . implode(' OR ', $klammer) . ')';
					$ranking .= '-' . $bed2;
					$bedingungen2_sql[] = $bed2;
				}
			}

			if(isset($bedingungen2_sql) && $bedingungen2_sql){
				$bedingung_sql = ' ( ( ' . implode(' OR ', $bedingungen2_sql) . (isset($bedingungen_sql) && $bedingungen_sql ? (' ) AND ' . implode(' AND ', $bedingungen_sql)) : ' ) ') . ' ) ';
			} else if(isset($bedingungen_sql) && $bedingungen_sql){
				$bedingung_sql = implode(' AND ', $bedingungen_sql);
			}


			$extraSelect = ',' . ($random ? ' RAND() as RANDOM ' : $ranking . ' AS ranking ') . $calendar_select;
			$limit = (($this->maxItemsPerPage > 0) ? (' LIMIT ' . abs($this->start) . ',' . abs($this->maxItemsPerPage)) : '');
		} else {
			if($this->workspaceID){
				$workspaces = explode(',', $this->workspaceID);
				if($this->subfolders){ // all entries with given parentIds
					$cond = array();
					$workspacePaths = id_to_path($workspaces, FILE_TABLE, $this->DB_WE);
					foreach($workspacePaths as $workspace){
						$cond[] = 'Path LIKE "' . $this->DB_WE->escape($workspace) . '/%"';
					}
					$this->DB_WE->query('SELECT ID FROM ' . FILE_TABLE . ' WHERE IsFolder=1 AND (' . implode(' OR ', $cond) . ')');
					$workspaces = array_unique(array_merge($workspaces, $this->DB_WE->getAll(true)));
				}
				$ws_where = ' AND (ParentID IN (' . implode(', ', $workspaces) . '))';
			}
			$extraSelect = ($random ? ', RAND() as RANDOM' : '');
			$limit = (($rows > 0) ? (' LIMIT ' . abs($this->start) . ',' . abs($this->maxItemsPerPage)) : "");
		}
		$this->DB_WE->query(
			'SELECT ' . FILE_TABLE . '.ID, ' . FILE_TABLE . '.WebUserID' . $extraSelect .
			' FROM ' . FILE_TABLE . ' LEFT JOIN ' . LINK_TABLE . ' l ON (' . FILE_TABLE . '.ID=l.DID AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '") LEFT JOIN ' . CONTENT_TABLE . ' c ON l.CID=c.ID ' . $joinstring .
			($this->search ? ' LEFT JOIN ' . INDEX_TABLE . ' i ON (i.ID=' . FILE_TABLE . '.ID AND i.ClassID=0) LEFT JOIN ' . FILE_TABLE . ' wsp ON wsp.ID=i.WorkspaceID ' : '') .
			' WHERE ' . $orderwhereString .
			($this->searchable ? ' ' . FILE_TABLE . '.IsSearchable=1' : 1) . ' ' .
			$where_lang . ' ' .
			$cond_where . ' ' .
			$ws_where . ' AND ' .
			FILE_TABLE . '.IsFolder=0 AND ' . FILE_TABLE . '.Published>0 ' .
			(isset($bedingung_sql) ? ' AND ' . $bedingung_sql : '') .
			($dt > 0 ? (' AND ' . FILE_TABLE . '.DocType=' . intval($dt)) : '') .
			' ' . $sql_tail . $calendar_where . ' GROUP BY ' . $this->group . ' ' . $orderstring .
			$limit
		);

		$this->anz = $this->DB_WE->num_rows();

		$_idListArray = array();

		while($this->DB_WE->next_record()){
			$this->IDs[] = $this->DB_WE->f('ID');
			if($calendar != ''){
				$this->calendar_struct['storage'][$this->DB_WE->f('ID')] = intval($this->DB_WE->f('Calendar'));
			}
			if($this->customers === '*' && intval($this->DB_WE->f('WebUserID')) > 0){
				$_idListArray[] = $this->DB_WE->f('WebUserID');
			}
		}
		if($this->customers === '*' && $_idListArray){
			$this->DB_WE->query('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID IN(' . implode(',', array_unique($_idListArray)) . ')');
			$encrypted = we_customer_customer::getEncryptedFields();
			while($this->DB_WE->next_record(MYSQL_ASSOC)){
				$this->customerArray['cid_' . $this->DB_WE->f('ID')] = array_merge($this->DB_WE->getRecord(), $encrypted);
			}
			unset($_idListArray);
		}

		$this->DB_WE->query(
			'SELECT ' . FILE_TABLE . '.ID as ID, ' . FILE_TABLE . '.WebUserID as WebUserID' .
			($random ? ',RAND() as RANDOM' : ($this->search ? ',' . $ranking . ' AS ranking' : '')) .
			' FROM ' . FILE_TABLE . ' JOIN ' . LINK_TABLE . ' l ON ' . FILE_TABLE . '.ID=l.DID JOIN ' . CONTENT_TABLE . ' c ON l.CID=c.ID' .
			($this->search ? ' LEFT JOIN ' . INDEX_TABLE . ' i ON (i.ID=' . FILE_TABLE . '.ID AND i.ClassID=0)' : '') .
			$joinstring .
			' WHERE ' .
			$orderwhereString .
			($this->searchable ? ' ' . FILE_TABLE . '.IsSearchable=1' : '1') . ' ' .
			$where_lang . ' ' .
			$cond_where . ' ' .
			$ws_where .
			' AND ' . FILE_TABLE . '.IsFolder=0 AND ' . FILE_TABLE . '.Published>0 AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"' .
			($this->search ? ' AND ' . $bedingung_sql : '') .
			($dt > 0 ? (' AND ' . FILE_TABLE . '.DocType=' . intval($dt)) : '') . ' ' .
			$sql_tail .
			$calendar_where .
			' GROUP BY ' . $this->group . ' ' . $orderstring);

		$this->anz_all = $this->DB_WE->num_rows();
		if($calendar != ''){
			$this->postFetchCalendar();
		}
	}

	public function next_record(){
		if($this->count < $this->anz){
			$count = $this->count;
			$fetch = false;
			if($this->calendar_struct['calendar']){
				parent::next_record();
				$count = $this->calendar_struct['count'];
				$fetch = $this->calendar_struct['forceFetch'];
			}

			if(!$this->calendar_struct['calendar'] || $fetch){
				$id = $this->IDs[$count];
				$this->DB_WE->query('SELECT l.Name,IF(c.BDID!=0,c.BDID,c.Dat) AS data FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON l.CID=c.ID WHERE l.DID=' . intval($id) . ' AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"');
				$this->Record = array_merge($this->DB_WE->getAllFirst(false), getHash('SELECT
	ID AS wedoc_ID,
	ID AS WE_ID,
	ParentID AS wedoc_ParentID,
	Text AS wedoc_Text,
	Text AS WE_TEXT,
	IsFolder AS wedoc_IsFolder,
	ContentType AS wedoc_ContentType,
	CreationDate AS wedoc_CreationDate,
	ModDate AS wedoc_ModDate,
	Path AS wedoc_Path,
	Path AS WE_PATH,
	TemplateID AS wedoc_TemplateID,
	Filename AS wedoc_Filename,
	Extension AS wedoc_Extension,
	IsDynamic AS wedoc_IsDynamic,
	IsSearchable AS wedoc_IsSearchable,
	DocType AS wedoc_DocType,
	ClassName AS wedoc_ClassName,
	Category AS wedoc_Category,
	Published AS wedoc_Published,
	CreatorID AS wedoc_CreatorID,
	ModifierID AS wedoc_ModifierID,
	RestrictOwners AS wedoc_RestrictOwners,
	Owners AS wedoc_Owners,
	OwnersReadOnly AS wedoc_OwnersReadOnly,
	Language AS wedoc_Language,
	WebUserID AS wedoc_WebUserID,
	InGlossar AS wedoc_InGlossar
FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), $this->DB_WE, MYSQL_ASSOC)
				);

				$this->Record['WE_SHOPVARIANTS'] = 0; //check this for global variants
				if(!empty($this->Record[we_base_constants::WE_VARIANTS_ELEMENT_NAME])){
					$variants = is_string($this->Record[we_base_constants::WE_VARIANTS_ELEMENT_NAME]) ? we_unserialize($this->Record[we_base_constants::WE_VARIANTS_ELEMENT_NAME]) : array();
					if(is_array($variants) && count($variants) > 0){
						$this->Record['WE_SHOPVARIANTS'] = count($variants);
					}
				}

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
		}
		$this->stop_next_row = $this->shouldPrintEndTR();
		if($this->cols && ($this->count <= $this->maxItemsPerPage) && !$this->stop_next_row){
			$this->Record = array();
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

	function makeConditionSql($cond){
		$cond = str_replace(array('&gt;', '&lt;'), array('>', '<'), $cond);

		$arr = explode(' ', $cond);

		$logic = array(
			'and' => array($arr[0]),
			'or' => array(),
			'not' => array(),
		);
		$current = 'and';
		$c = 0;
		for($i = 1; $i < count($arr); $i++){
			$elem = strtolower($arr[$i]);
			if(in_array($elem, array_keys($logic))){
				$c = count($logic[$current]);
				$current = $elem;
			} else {
				if(isset($logic[$current][$c])){
					$logic[$current][$c].=' ' . $arr[$i];
				} else {
					$logic[$current][$c] = $arr[$i];
				}
			}
		}

		$sqlarr = '';
		$patterns = array('<>', '!=', '<=', '>=', '=', '<', '>', 'LIKE', 'IN');
		foreach($logic as $oper => $arr){
			foreach($arr as $exp){
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
		return '(l.nHash=x\'' . md5($name) . '\' AND c.Dat ' . $operation . ' ' . $value . ')';
	}

}
