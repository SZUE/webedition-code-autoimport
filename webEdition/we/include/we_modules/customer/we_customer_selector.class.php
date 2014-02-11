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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class we_customer_selector extends we_users_selector{

	function __construct($id, $JSIDName = '', $JSTextName = '', $JSCommand = '', $order = '', $sessionID = '', $rootDirID = 0, $filter = '', $multiple = true){
		$mid = (is_numeric($id) ? $id : 0);

		parent::__construct($mid, CUSTOMER_TABLE, $JSIDName, $JSTextName, $JSCommand, $order, $sessionID, $rootDirID, $multiple, $filter);
		$this->title = g_l('fileselector', '[userSelector][title]');
		$this->canSelectDir = false;
		$this->dir = (is_numeric($id) ? '' : $id);
		if($this->dir){
			$_SESSION['weS']['we_fs_lastDir'][$this->table] = $this->dir;
		} else {
			$this->setDefaultDirAndID(false);
		}
	}

	function setDirAndID(){
		if(!$this->dir){
			parent::setDirAndID();
		}
	}

	function setDefaultDirAndID($setLastDir){
		if($setLastDir){
			$this->dir = $_SESSION['weS']['we_fs_lastDir'][$this->table];
			if($this->dir == ''){
				$this->dir = '';
			}
			return;
		}
		if($this->id){
			while($this->query($this->id) && $this->db->next_record()){
				$this->dir = $this->db->f('Path');
			}
			$_SESSION['weS']['we_fs_lastDir'][$this->table] = $this->dir;
		}
	}

	function query($optionalID = 0){
		$pid = $this->dir;
		$settings = new we_customer_settings();
		$settings->load();
		$sort = $settings->getSettings('default_sort_view');
		if($sort == g_l('modules_customer', '[no_sort]')){
			$sort = '';
		}
		if($sort){
			$havingarr = $sort_defs = $pidarr = $check = array();

			if($pid){
				$pidarr = explode('-|-', $pid);
				unset($pidarr[count($pidarr) - 1]);
			}

			if(isset($settings->SortView[$sort])){
				$sort_defs = $settings->SortView[$sort];
			}

			$select = $grouparr = $orderarr = array();

			foreach($sort_defs as $c => $sortdef){
				if(isset($sortdef['function']) && $sortdef['function']){
					$select[] = '@select' . count($select) . ':=' . ($settings->customer->isInfoDate($sortdef['field']) ?
									sprintf($settings->FunctionTable[$sortdef['function']], 'FROM_UNIXTIME(' . $sortdef['field'] . ')') . ' AS ' . $sortdef['field'] . "_" . $sortdef["function"] :
									sprintf($settings->FunctionTable[$sortdef['function']], $sortdef['field']) . ' AS ' . $sortdef['field'] . '_' . $sortdef['function']);

					$grouparr[] = $sortdef['field'] . '_' . $sortdef['function'];
					$orderarr[] = $sortdef['field'] . '_' . $sortdef['function'] . ' ' . $sortdef['order'];
					$orderarr[] = $sortdef['field'] . ' ' . $sortdef['order'];
					if(isset($pidarr[$c])){
						$havingarr[] = (empty($pidarr[$c]) ?
										'(' . $sortdef['field'] . '_' . $sortdef["function"] . "='' OR " . $sortdef['field'] . '_' . $sortdef['function'] . ' IS NULL)' :
										$sortdef['field'] . '_' . $sortdef['function'] . "='" . $pidarr[$c] . "'");
					}
				} else {
					$select[] = '@select' . count($select) . ':=' . $sortdef['field'];
					$grouparr[] = $sortdef['field'];
					$orderarr[] = $sortdef['field'] . ' ' . $sortdef['order'];
					if(isset($pidarr[$c]) && $pidarr[$c]){
						$havingarr[] = (empty($pidarr[$c]) ?
										'(' . $sortdef['field'] . "='' OR " . $sortdef['field'] . ' IS NULL)' :
										$sortdef['field'] . "='" . $pidarr[$c] . "'");
					}
				}
			}

			$level = count($pidarr);
			$levelcount = count($grouparr);

			$grp = implode(',', array_slice($grouparr, 0, $level + 1));

			if($level < $levelcount){
				$fields = 'ID,"' . we_base_ContentTypes::FOLDER_ICON . '" AS Icon,1 AS IsFolder' . (empty($select) ? '' : ',' . implode(',', $select) ) . ', COALESCE(NULLIF(@select' . $level . ',""),"' . g_l('modules_customer', '[no_value]') . '") AS Text,CONCAT("' . $pid . '",@select' . $level . ',"-|-") AS Path';
			} else {
				$fields = $settings->treeTextFormatSQL . ' AS Text,ID,ParentID,Path,Icon,0 AS IsFolder' . (empty($select) ? '' : ',' . implode(',', $select) );
			}

			$this->db->query('SELECT ' . $fields . ' FROM ' . CUSTOMER_TABLE . ' WHERE ' .
					($optionalID ? ' ID=' . intval($optionalID) : '1') . ' AND ' .
					(!permissionhandler::hasPerm("ADMINISTRATOR") && $_SESSION['user']['workSpace'][CUSTOMER_TABLE] ? $_SESSION['user']['workSpace'][CUSTOMER_TABLE] : '1 ') .
					' GROUP BY ' . $grp . (count($grouparr) ? ($level != 0 ? ',ID' : '') : 'ID') . (count($havingarr) ? ' HAVING ' . implode(' AND ', $havingarr) : '') .
					' ORDER BY ' . implode(',', $orderarr) . we_customer_treeLoader::getSortOrder($settings, ','));
			return ($level < $levelcount);
		} else {
			$this->db->query('SELECT ID,ParentID,Path,IsFolder,Icon,' . $settings->treeTextFormatSQL . ' AS Text FROM ' . CUSTOMER_TABLE .
					' WHERE ' . (!permissionhandler::hasPerm("ADMINISTRATOR") && $_SESSION['user']['workSpace'][CUSTOMER_TABLE] ? $_SESSION['user']['workSpace'][CUSTOMER_TABLE] : '1 ') . we_customer_treeLoader::getSortOrder($settings));
			//no need to search directory
			return false;
		}
	}

	function printSetDirHTML(){
		print '<script type="text/javascript"><!--
top.clearEntries();
' .
				$this->printCmdAddEntriesHTML() .
				$this->printCMDWriteAndFillSelectorHTML() .
				'top.fsheader.' . (empty($this->dir) ? 'disable' : 'enable') . 'RootDirButs();';

		if(permissionhandler::hasPerm("ADMINISTRATOR")){
			$go = true;
		}
		if($go){
			if($this->id == 0){
				$this->path = '/';
			}
			print 'top.currentPath = "' . $this->path . '";
top.currentID = "' . $this->id . '";';
//top.fsfooter.document.we_form.fname.value = "' . $this->values["Text"] . '";';
		}
		$_SESSION['weS']['we_fs_lastDir'][$this->table] = $this->dir;
//top.parentID = "' . $this->values["ParentID"] . '";
		print 'top.currentDir = "' . $this->dir . '";
//-->
</script>';
	}

	function printFramesetJSsetDir(){
		return we_html_element::jsElement('
function setDir(id){
	if(id>=0 || id<0){
		e = getEntry(id);
		currentDir = id;
		path=e.path;
		if(path=="/"){
			path=0;
		}
	}else{
		path=id;
	}
	top.fscmd.location.replace(top.queryString(' . we_selector_multiple::SETDIR . ',path));
}'
		);
	}

	private function getHeaderElements(){
		$vals = explode('-|-', $this->dir);
		unset($vals[count($vals) - 1]);
		$out = array('0' => '/');
		foreach($vals as $pos => $val){
			$out[implode('-|-', array_slice($vals, 0, $pos + 1)) . '-|-'] = ($val ? $val : g_l('modules_customer', '[no_value]'));
		}
		return $out;
	}

	function printHeaderOptions(){
		$elem = $this->getHeaderElements();
		end($elem);
		$last = key($elem);
		$out = '';
		foreach($elem as $key => $val){
			$out.='<option value="' . $key . '"' . ($last == $key ? ' selected="selected"' : '') . '>' . $val . '</option>';
		}
		return $out;
	}

	function printCMDWriteAndFillSelectorHTML(){
		$elem = $this->getHeaderElements();
		$out = '';
		foreach($elem as $key => $val){
			$out .= 'top.fsheader.addOption("' . $val . '","' . $key . '");';
		}

		return '
top.writeBody(top.fsbody.document);
top.fsheader.clearOptions();' .
				$out . '
top.fsheader.selectIt();';
	}

}
