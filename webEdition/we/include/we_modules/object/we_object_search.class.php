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
class we_object_search extends we_search_base{
	var $height;
	var $searchname;
	var $searchlocation;
	var $searchfield;
	var $show;
	private static $intFields = array();
	private static $realFields = array();

	function __construct(){
		parent::__construct();
		if(isset($sessDat) && is_array($sessDat)){
			for($i = 0; $i <= count($sessDat); $i++){
				if(isset($sessDat[$i])){
					$v = $sessDat[$i];
					$this->$sessDat[$i] = $v;
				}
			}
		}
	}

	function init($sessDat = ""){
		for($i = 0; $i <= count($sessDat); $i++){
			if(isset($sessDat[$i])){
				$v = $sessDat[$i];
				$this->$sessDat[$i] = $v;
			}
		}
	}

	function getFields($name, $size, $select, $Path, $multi = ""){
		$objID = f('SELECT ID FROM ' . OBJECT_TABLE . ' WHERE Path="' . $GLOBALS['DB_WE']->escape($Path) . '"');
		if(!$objID){
			return '';
		}
		$opts = '';
		$all = array();
		$tableInfo = $GLOBALS['DB_WE']->metadata(OBJECT_X_TABLE . $objID);
		foreach($tableInfo as $cur){
			if($cur["name"] != 'ID' && substr($cur["name"], 0, 3) != "OF_" && stripos($cur["name"], we_objectFile::TYPE_MULTIOBJECT) !== 0 && stripos($cur["name"], "object") !== 0){
				$regs = explode('_', $cur["name"], 2);
				if(count($regs) == 2){
					$opts .= '<option value="' . $cur["name"] . '" '
						. (($select == $cur["name"]) ? "selected" : "") . '>'
						. $regs[1] . '</option>';
				}
				$all[] = $cur["name"];
			} else {
				switch($cur["name"]){
					case 'OF_Text':
						$opts .= '<option value="' . $cur["name"] . '" ' . (($select == $cur["name"]) ? "selected" : "") . '>' . g_l('modules_object', '[objectname]') . '</option>';
						$all[] = $cur["name"];
						break;
					case 'OF_Path':
						$opts .= '<option value="' . $cur["name"] . '" ' . (($select == $cur["name"]) ? "selected" : "") . '>' . g_l('modules_object', '[objectpath]') . '</option>';
						$all[] = $cur["name"];
						break;
					case 'OF_ID':
						$opts .= '<option value="' . $cur["name"] . '" ' . (($select == $cur["name"]) ? "selected" : "") . '>' . g_l('modules_object', '[objectid]') . '</option>';
						$all[] = $cur["name"];
						break;
					case 'OF_Url':
						$opts .= '<option value="' . $cur["name"] . '" ' . (($select == $cur["name"]) ? "selected" : "") . '>' . g_l('modules_object', '[objecturl]') . '</option>';
						$all[] = $cur["name"];
						break;
				}
			}
		}

		$opts = '<option value="' . implode(',', $all) . '">' . g_l('modules_object', '[allFields]') . '</option>' . $opts;
		$onchange = (substr($select, 0, 4) != "meta" && substr($select, 0, 4) != "date" && substr($select, 0, 8) != "checkbox" ? 'onchange="changeit(this.value);"' : 'onchange="changeitanyway(this.value);"');
		return '<select name="' . $name . '" class="weSelect" size="' . $size . '" ' . $multi . ' ' . $onchange . '>' . $opts . '</select>';
	}

	function getJSinWEsearchobj($name){
		return we_html_element::jsElement('
_EditorFrame.setEditorIsHot(false);

function next(){
	document.we_form.elements.SearchStart.value = parseInt(document.we_form.elements.SearchStart.value) + ' . $this->anzahl . ';
	top.we_cmd("reload_editpage");
}
function back(){
	document.we_form.elements.SearchStart.value = parseInt(document.we_form.elements.SearchStart.value) - ' . $this->anzahl . ';
	top.we_cmd("reload_editpage");
}

function setOrder(order){

	foo = document.we_form.elements.Order.value;

	if(((foo.substring(foo.length-5,foo.length) == " DESC") && (foo.substring(0,order.length-5) == order)) || foo != order){
		document.we_form.elements.Order.value=order;
	}else{
		document.we_form.elements.Order.value=order+" DESC";
	}
	top.we_cmd("reload_editpage");
}

function setWs(path,id) {
	document.we_form.elements["we_' . $name . '_WorkspacePath"].value=path;
	document.we_form.elements["we_' . $name . '_WorkspaceID"].value=id;
	top.we_cmd("reload_editpage");
}

function toggleShowVisible(c) {
	c.value=(c.checked ? 1 : 0);
	document.we_form.elements.SearchStart.value = 0;
	top.we_cmd("reload_editpage");
}');
	}

	function greenOnly($GreenOnly, $pid, $cid){
		if($GreenOnly){
			$pid_tail = we_objectFile::makePIDTail($pid, $cid, $GLOBALS['DB_WE']);
			return ' AND ' . OBJECT_X_TABLE . intval($cid) . '.OF_Published > 0 AND ' . $pid_tail;
		}
	}

	function getExtraWorkspace($exws, $we_extraWsLength, $id, $userWSArray){
		if(empty($exws)){
			return "-";
		}
		$isAdmin = permissionhandler::hasPerm("ADMINISTRATOR");
		$out = '<table class="default">';
		for($i = 0; $i < count($exws); $i++){
			if($exws[$i] != ""){

				$checkbox = ($isAdmin || we_users_util::in_workspace($exws[$i], $userWSArray) ?
						'<a href="javascript:we_cmd(\'object_toggleExtraWorkspace\',\'' . $GLOBALS["we_transaction"] . '\',\'' . $this->db->f("ID") . '\',\'' . $exws[$i] . '\',\'' . $id . '\')"><i name="check_' . $id . '_' . $this->db->f("ID") . '" class="fa fa-' . (strstr($this->db->f("OF_ExtraWorkspacesSelected"), "," . $exws[$i] . ",") ? 'check-' : '') . 'square-o wecheckIcon"></i></a>' :
						'<i name="check_' . $id . '_' . $this->db->f("ID") . '" class="fa fa-' . (strstr($this->db->f("OF_ExtraWorkspacesSelected"), "," . $exws[$i] . ",") ? 'check-' : '') . 'square-o wecheckIcon"></i>'
					);

				$p = id_to_path($exws[$i]);
				$out .= '
<tr>
	<td>' . $checkbox . '</td>
	<td style="width:5px;"></td>
	<td class="middlefont">&nbsp;<a href="javascript:setWs(\'' . $p . '\',\'' . $exws[$i] . '\')" class="middlefont" title="' . $p . '">' . we_base_util::shortenPath($p, $we_extraWsLength) . '</a><td>
</tr>';
			}
		}

		return $out . '</table>';
	}

	function getWorkspaces(array $foo, $we_wsLength){
		if(!$foo){
			return '-';
		}
		$out = '<table class="default">';
		foreach($foo as $cur){
			if($cur != ""){
				$p = id_to_path($cur);
//				$pl = strlen($p);
				$out .= '
<tr>
	<td class="middlefont">
		&nbsp;<a href="javascript:setWs(\'' . $p . '\',\'' . $cur . '\')" class="middlefont" title="' . $p . '">' . we_base_util::shortenPath($p, $we_wsLength) . '</a><td>
</tr>';
			}
		}

		return $out . '</table>';
	}

	function searchfor($searchname, $searchfield, $searchlocation, $tablename, $rows = -1, $start = 0, $order = '', $desc = 0){
		for($i = 0; $i < count($searchname); $i++){
			$filteredFields = '';
			$fieldsToFilterOut = array();

			$type = !preg_match('/^[-+]?\d*\.?\d+$/', $searchname[$i]) ? 1 : (!preg_match('/^[-+]?\d+$/', $searchname[$i]) ? 2 : 0);
			switch($type){
				case 1:
					$fieldsToFilterOut = array_merge($fieldsToFilterOut, $this->getRealFields($tablename));
				//no break!
				case 2:
					$fieldsToFilterOut = array_merge($fieldsToFilterOut, $this->getIntFields($tablename));
			}

			if($fieldsToFilterOut){
				$arrSearchfield = explode(',', trim($searchfield[$i], ','));
				foreach($arrSearchfield as $f){
					if(!in_array($f, $fieldsToFilterOut)){
						$filteredFields .= $f . ',';
					}
				}
				$searchfield[$i] = rtrim($filteredFields, ',');
			}
		}

		return parent::searchfor($searchname, $searchfield, $searchlocation, $tablename, $rows, $start, $order, $desc);
	}

	private function getIntFields($tablename){
		if(self::$intFields || !$tablename){
			return self::$intFields;
		}

		foreach($this->db->metadata($tablename) as $f){
			if(in_array($f['type'], array('int', 'tinyint', 'smallint', 'mediumint', 'bigint'))){
				self::$intFields[] = $f['name'];
			}
		}
		return self::$intFields;
	}

	private function getRealFields($tablename){
		if(self::$realFields || !$tablename){
			return self::$realFields;
		}

		foreach($this->db->metadata($tablename) as $f){
			if(in_array($f['type'], array('real', 'float', 'double', 'decimal'))){
				self::$realFields[] = $f['name'];
			}
		}
		return self::$realFields;
	}

	function removeFilter($position){

		foreach($this->objsearch as $idx => $value){
			if($idx == $position){
				unset($this->objsearch[$position]);
			} elseif($idx >= $position){
				if(!isset($this->objsearch[($idx - 1)])){
					$this->objsearch[($idx - 1)] = "";
				}
				$this->objsearch[($idx - 1)] = $this->objsearch[$idx];
				unset($this->objsearch[$idx]);
			}
		}

		foreach($this->objsearchField as $idx => $value){
			if($idx == $position){
				unset($this->objsearchField[$position]);
			} elseif($idx >= $position){
				if(!isset($this->objsearchField[($idx - 1)])){
					$this->objsearchField[($idx - 1)] = "";
				}
				$this->objsearchField[($idx - 1)] = $this->objsearchField[$idx];
				unset($this->objsearchField[$idx]);
			}
		}

		foreach($this->objlocation as $idx => $value){
			if($idx == $position){
				unset($this->objlocation[$position]);
			} elseif($idx >= $position){
				if(!isset($this->objlocation[($idx - 1)])){
					$this->objlocation[($idx - 1)] = "";
				}
				$this->objlocation[($idx - 1)] = $this->objlocation[$idx];
				unset($this->objlocation[$idx]);
			}
		}

		$this->height--;
	}

}
