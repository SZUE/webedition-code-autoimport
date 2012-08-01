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
class we_schedpro{

	const DELETE = 3;
	const DOCTYPE = 4;
	const CATEGORY = 5;
	const DIR = 6;
	const TYPE_ONCE = 0;
	const TYPE_HOUR = 1;
	const TYPE_DAY = 2;
	const TYPE_WEEK = 3;
	const TYPE_MONTH = 4;
	const TYPE_YEAR = 5;
	const SCHEDULE_FROM = 1;
	const SCHEDULE_TO = 2;

	var $task = 1;
	var $type = 0;
	var $months = array();
	var $days = array();
	var $weekdays = array();
	var $time = 0;
	var $nr = 0;
	var $CategoryIDs = "";
	var $DoctypeID = 0;
	var $ParentID = 0;
	var $active = 1;
	var $doctypeAll = 0;

	function __construct($s = "", $nr = 0){
		if(is_array($s)){
			$this->task = isset($s["task"]) ? $s["task"] : 1;
			$this->type = isset($s["type"]) ? $s["type"] : 0;
			$this->months = isset($s["months"]) ? $s["months"] : array();
			$this->days = isset($s["days"]) ? $s["days"] : array();
			$this->weekdays = isset($s["weekdays"]) ? $s["weekdays"] : array();
			$this->time = isset($s["time"]) ? $s["time"] : time();
			$this->CategoryIDs = isset($s["CategoryIDs"]) ? $s["CategoryIDs"] : "";
			$this->DoctypeID = isset($s["DoctypeID"]) ? $s["DoctypeID"] : 0;
			$this->ParentID = isset($s["ParentID"]) ? $s["ParentID"] : 0;
			$this->active = isset($s["active"]) ? $s["active"] : 1;
			$this->doctypeAll = isset($s["doctypeAll"]) ? $s["doctypeAll"] : 0;
			;
		} else{
			$this->task = 1;
			$this->type = 0;
			$this->months = array();
			$this->days = array();
			$this->weekdays = array();
			$this->time = time();
			$this->CategoryIDs = "";
			$this->DoctypeID = 0;
			$this->ParentID = 0;
			$this->active = 1;
			$this->doctypeAll = 0;
		}
		$this->nr = $nr;
	}

	function getMonthsHTML(){
		$months = '<table cellpadding="0" cellspacing="0" border="0"><tr>
';

		for($i = 1; $i <= 12; $i++){
			$months .= '<td>' . we_forms::checkbox("1", $this->months[$i - 1], "check_we_schedule_month" . $i . "_" . $this->nr, g_l('date', '[month][short][' . ($i - 1) . ']'), false, "defaultfont", "this.form.elements['we_schedule_month" . $i . "_" . $this->nr . "'].value=this.checked?1:0;_EditorFrame.setEditorIsHot(true)") .
				'<input type="hidden" name="we_schedule_month' . $i . '_' . $this->nr . '" value="' . $this->months[$i - 1] . '" /></td>';
		}

		$months .= '</tr></table>
';
		return $months;
	}

	function getDaysHTML(){
		$days = '<table cellpadding="0" cellspacing="0" border="0"><tr>';

		for($i = 1; $i <= 36; $i++){
			if($i <= 31){
				$days .= '<td>' . we_forms::checkbox("1", $this->days[$i - 1], "check_we_schedule_day" . $i . "_" . $this->nr, sprintf('%02d', $i), false, "defaultfont", "this.form.elements['we_schedule_day" . $i . "_" . $this->nr . "'].value=this.checked?1:0;_EditorFrame.setEditorIsHot(true)")
					. '<input type="hidden" name="we_schedule_day' . $i . '_' . $this->nr . '" value="' . $this->days[$i - 1] . '" /></td><td class="defaultfont">&nbsp;</td>';
			} else{
				$days .= '<td colspan="3">';
			}
			switch($i){
				case 14:
				case 28:
					$days .= '</tr><tr>';
					break;
			}
		}

		$days .= '</tr></table>';
		return $days;
	}

	function getWeekdaysHTML(){
		$wd = '<table cellpadding="0" cellspacing="0" border="0"><tr>';

		for($i = 1; $i <= 7; $i++){
			$wd .= '<td>' . we_forms::checkbox("1", $this->weekdays[$i - 1], "check_we_schedule_wday'.$i.'_'.$this->nr.'", g_l('date', '[day][short][' . ($i - 1) . ']'), false, "defaultfont", "this.form.elements['we_schedule_wday" . $i . "_" . $this->nr . "'].value=this.checked?1:0;_EditorFrame.setEditorIsHot(true)")
				. '<input type="hidden" name="we_schedule_wday' . $i . '_' . $this->nr . '" value="' . $this->weekdays[$i - 1] . '" /></td><td class="defaultfont">&nbsp;</td>';
		}

		$wd .= '</tr></table>';
		return $wd;
	}

	function getSpacerRowHTML(){
		return '	<tr valign="top">
		<td>' . we_html_tools::getPixel(80, 10) . '</td>
		<td>' . we_html_tools::getPixel(565, 10) . '</td>
		<td>' . we_html_tools::getPixel(26, 10) . '</td>
	</tr>
';
	}

	function getHTML($isobj = false){
		$taskpopup = '<select class="weSelect" name="we_schedule_task_' . $this->nr . '" size="1" onchange="_EditorFrame.setEditorIsHot(true);if(self.we_hasExtraRow_' . $this->nr . ' || this.options[this.selectedIndex].value==' . self::DOCTYPE . ' || this.options[this.selectedIndex].value==' . self::CATEGORY . ' || this.options[this.selectedIndex].value==' . self::DIR . '){ setScrollTo();we_cmd(\'reload_editpage\');}">
<option value="' . self::SCHEDULE_FROM . '"' . (($this->task == self::SCHEDULE_FROM) ? ' selected' : '') . '>' . g_l('modules_schedule', "[task][" . self::SCHEDULE_FROM . ']') . '</option>
<option value="' . self::SCHEDULE_TO . '"' . (($this->task == self::SCHEDULE_TO) ? ' selected' : '') . '>' . g_l('modules_schedule', "[task][" . self::SCHEDULE_TO . ']') . '</option>';
		if((we_hasPerm("DELETE_DOCUMENT") && (!$isobj)) || (we_hasPerm("DELETE_OBJECTFILE") && $isobj)){
			$taskpopup .= '<option value="' . self::DELETE . '"' . (($this->task == self::DELETE) ? ' selected' : '') . '>' . g_l('modules_schedule', "[task][" . self::DELETE . ']') . '</option>';
		}
		if(!$isobj){
			$taskpopup .= '<option value="' . self::DOCTYPE . '"' . (($this->task == self::DOCTYPE) ? ' selected' : '') . '>' . g_l('modules_schedule', "[task][" . self::DOCTYPE . ']') . '</option>';
		}
		$taskpopup .= '<option value="' . self::CATEGORY . '"' . (($this->task == self::CATEGORY) ? ' selected' : '') . '>' . g_l('modules_schedule', "[task][" . self::CATEGORY . ']') . '</option>';
		if((we_hasPerm("MOVE_DOCUMENT") && (!$isobj)) || (we_hasPerm("MOVE_OBJECTFILE") && $isobj)){
			$taskpopup .= '<option value="' . self::DIR . '"' . (($this->task == self::DIR) ? ' selected' : '') . '>' . g_l('modules_schedule', "[task][" . self::DIR . ']') . '</option>';
		}
		$taskpopup .= '</select>';
		$extracont = "";
		$extraheadl = "";


		switch($this->task){
			case self::DOCTYPE:
				$db = new DB_WE();
				$q = getDoctypeQuery($db);
				$db->query("SELECT ID,DocType FROM " . DOC_TYPES_TABLE . " $q");
				$doctypepop = '<select class="weSelect" name="we_schedule_doctype_' . $this->nr . '" size="1" onchange="_EditorFrame.setEditorIsHot(true)">';
				while($db->next_record()) {
					$doctypepop .= '<option value="' . $db->f("ID") . '"' . (($this->DoctypeID == $db->f("ID")) ? ' selected="selected"' : '') . '>' . $db->f("DocType") . '</option>';
				}
				$doctypepop .= '</select>';
				$checknname = md5(uniqid(rand(), 1));
				$extracont = '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . $doctypepop . '</td><td class="defaultfont">&nbsp;&nbsp;</td><td>' . we_forms::checkbox("1", $this->doctypeAll, $checknname, g_l('modules_schedule', "[doctypeAll]")
						, false, "defaultfont", "this.form.elements['we_schedule_doctypeAll_" . $this->nr . "'].value=this.checked?1:0;")
					. '<input type="hidden" name="we_schedule_doctypeAll_' . $this->nr . '" value="' . $this->doctypeAll . '" /></td></tr></table>';
				$extraheadl = g_l('modules_schedule', "[doctype]");
				break;
			case self::CATEGORY:
				$delallbut = we_button::create_button("delete_all", "javascript:we_cmd('delete_all_schedcats'," . $this->nr . ")");
				$addbut = we_button::create_button("add", "javascript:we_cmd('openCatselector','','" . CATEGORY_TABLE . "','','','opener.setScrollTo();opener.top.we_cmd(\\'add_schedcat\\',top.currentID," . $this->nr . ");')");
				$cats = new MultiDirChooser(450, $this->CategoryIDs, "delete_schedcat", we_button::create_button_table(array($delallbut, $addbut)), "", "Icon,Path", CATEGORY_TABLE, "defaultfont", $this->nr);
				$cats->extraDelFn = 'setScrollTo();';
				if(!we_hasPerm("EDIT_KATEGORIE")){
					$cats->isEditable = false;
				}
				$extracont = $cats->get();
				$extraheadl = g_l('modules_schedule', "[categories]");
				break;
			case self::DIR:
				$textname = 'path_we_schedule_parentid_' . $this->nr;
				$idname = 'we_schedule_parentid_' . $this->nr;
				$myid = $this->ParentID;
				$path = id_to_path($this->ParentID, $GLOBALS['we_doc']->Table);

				if($GLOBALS['we_doc']->ClassName == "we_objectFile"){
					if($path == "/"){ //	impossible for documents
						$path = $GLOBALS['we_doc']->RootDirPath;
					}
					$_rootDirID = $GLOBALS['we_doc']->rootDirID;
				} else{
					$_rootDirID = 0;
				}

				$wecmdenc1 = we_cmd_enc('document.we_form.elements[\'' . $idname . '\'].value');
				$wecmdenc2 = we_cmd_enc('document.we_form.elements[\'' . $textname . '\'].value');
				$wecmdenc3 = we_cmd_enc('top.opener._EditorFrame.setEditorIsHot(true);');
				$button = we_button::create_button('select', 'javascript:we_cmd(\'openDirselector\',document.we_form.elements[\'' . $idname . '\'].value,\'' . $GLOBALS['we_doc']->Table . '\',\'' . $wecmdenc1 . '\',\'' . $wecmdenc2 . '\',\'' . $wecmdenc3 . '\',\'' . session_id() . '\',\'' . $_rootDirID . '\')');

				$yuiSuggest = & weSuggest::getInstance();
				$yuiSuggest->setAcId("WsDir");
				$yuiSuggest->setContentType("folder");
				$yuiSuggest->setInput($textname, $path);
				$yuiSuggest->setMaxResults(20);
				$yuiSuggest->setMayBeEmpty(0);
				$yuiSuggest->setResult($idname, $myid);
				$yuiSuggest->setSelector('Dirselector');
				$yuiSuggest->setTable(FILE_TABLE);
				$yuiSuggest->setWidth(320);
				$yuiSuggest->setSelectButton($button);

				$extracont = $yuiSuggest->getYuiFiles() . $yuiSuggest->getHTML() . $yuiSuggest->getYuiCode();
				$extraheadl = g_l('modules_schedule', "[dirctory]");
		}

		$typepopup = '<select class="weSelect" name="we_schedule_type_' . $this->nr . '" size="1" onchange="_EditorFrame.setEditorIsHot(true);setScrollTo();we_cmd(\'reload_editpage\')">
<option value="' . self::TYPE_ONCE . '"' . (($this->type == self::TYPE_ONCE) ? ' selected' : '') . '>' . g_l('modules_schedule', "[type][" . self::TYPE_ONCE . ']') . '</option>
<option value="' . self::TYPE_HOUR . '"' . (($this->type == self::TYPE_HOUR) ? ' selected' : '') . '>' . g_l('modules_schedule', "[type][" . self::TYPE_HOUR . ']') . '</option>
<option value="' . self::TYPE_DAY . '"' . (($this->type == self::TYPE_DAY) ? ' selected' : '') . '>' . g_l('modules_schedule', "[type][" . self::TYPE_DAY . ']') . '</option>
<option value="' . self::TYPE_WEEK . '"' . (($this->type == self::TYPE_WEEK) ? ' selected' : '') . '>' . g_l('modules_schedule', "[type][" . self::TYPE_WEEK . ']') . '</option>
<option value="' . self::TYPE_MONTH . '"' . (($this->type == self::TYPE_MONTH) ? ' selected' : '') . '>' . g_l('modules_schedule', "[type][" . self::TYPE_MONTH . ']') . '</option>
<option value="' . self::TYPE_YEAR . '"' . (($this->type == self::TYPE_YEAR) ? ' selected' : '') . '>' . g_l('modules_schedule', "[type][" . self::TYPE_YEAR . ']') . '</option>
</select>';


		$checknname = md5(uniqid(rand(), 1));
		$table = '<table cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', "[task][headline]") . ':</td>
		<td class="defaultfont"><table border="0" cellpadding="0" cellspacing="0"><tr><td>' . $taskpopup . '</td><td class="defaultfont">&nbsp;&nbsp;</td><td>' . we_forms::checkbox("1", $this->active, $checknname, g_l('modules_schedule', "[active]")
				, false, "defaultfont", "this.form.elements['we_schedule_active_" . $this->nr . "'].value=this.checked?1:0;_EditorFrame.setEditorIsHot(true);")
			. '<input type="hidden" name="we_schedule_active_' . $this->nr . '" value="' . $this->active . '" /></td></tr></table></td>
		<td>' . we_button::create_button("image:btn_function_trash", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('del_schedule','" . $this->nr . "')") . '</td>
	</tr>' . $this->getSpacerRowHTML();
		if($extracont){
			$table .= '	<tr valign="top">
		<td class="defaultgray">' . $extraheadl . ':</td>
		<td class="defaultfont">' . $extracont . '</td>
		<td></td>
	</tr>' . $this->getSpacerRowHTML();
		}

		$table .= '	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', "[type][headline]") . ':</td>
		<td class="defaultfont">' . $typepopup . '</td>
		<td></td>
	</tr>' .
			$this->getSpacerRowHTML();


		switch($this->type){
			case self::TYPE_ONCE:
				$table .= '	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', "[datetime]") . ':</td>
		<td class="defaultfont">' . we_html_tools::getDateInput2("we_schedule_time%s_" . $this->nr, $this->time, true) . '</td>
		<td></td>
	</tr>';
				break;
			case self::TYPE_HOUR:
				$table .= '	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', "[minutes]") . ':</td>
		<td class="defaultfont">' . we_html_tools::getDateInput2("we_schedule_time%s_" . $this->nr, $this->time, true, "i") . '</td>
		<td></td>
	</tr>';
				break;
			case self::TYPE_DAY:
				$table .= '	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', "[time]") . ':</td>
		<td class="defaultfont">' . we_html_tools::getDateInput2("we_schedule_time%s_" . $this->nr, $this->time, true, "h:i") . '</td>
		<td></td>
	</tr>';
				break;
			case self::TYPE_WEEK:
				$table .= '	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', "[time]") . ':</td>
		<td class="defaultfont">' . we_html_tools::getDateInput2("we_schedule_time%s_" . $this->nr, $this->time, true, "h:i") . '</td>
		<td></td>
	</tr>' .
					$this->getSpacerRowHTML() .
					'	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', "[weekdays]") . ':</td>
		<td class="defaultfont">' . $this->getWeekdaysHTML() . '</td>
		<td></td>
	</tr>';
				break;
			case self::TYPE_MONTH:
				$table .= '	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', "[time]") . ':</td>
		<td class="defaultfont">' . we_html_tools::getDateInput2("we_schedule_time%s_" . $this->nr, $this->time, true, "h:i") . '</td>
		<td></td>
	</tr>' .
					$this->getSpacerRowHTML() .
					'	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', "[days]") . ':</td>
		<td class="defaultfont">' . $this->getDaysHTML() . '</td>
		<td></td>
	</tr>';
				break;
			case self::TYPE_YEAR:
				$table .= '	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', "[time]") . ':</td>
		<td class="defaultfont">' . we_html_tools::getDateInput2("we_schedule_time%s_" . $this->nr, $this->time, true, "h:i") . '</td>
		<td></td>
	</tr>' .
					$this->getSpacerRowHTML() .
					'	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', "[months]") . ':</td>
		<td class="defaultfont">' . $this->getMonthsHTML() . '</td>
		<td></td>
	</tr>' .
					$this->getSpacerRowHTML() .
					'	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', "[days]") . ':</td>
		<td class="defaultfont">' . $this->getDaysHTML() . '</td>
		<td></td>
	</tr>';
				break;
		}
		$table .= '</table>' . "\n";
		return we_html_element::jsElement('var we_hasExtraRow_' . $this->nr . '=' . ($extracont ? 'true' : 'false')) . $table;
	}

	function processSchedule($id, $schedFile, $now, $DB_WE){
		usort($schedFile["value"], "weCmpSchedLast");

		$doc_save = isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc'] : NULL;
		$GLOBALS['we_doc'] = new $schedFile['ClassName']();
		$GLOBALS['we_doc']->InitByID($id, $schedFile["table"], we_class::LOAD_SCHEDULE_DB);
		$deleted = false;
		$changeTmpDoc = false;
		$_SESSION["Versions"]['fromScheduler'] = true;

		foreach($schedFile["value"] as $s){

			if($s["task"] == self::DELETE){
				$GLOBALS["NOT_PROTECT"] = true;
				include_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_delete_fn.inc.php");
				deleteEntry($id, $schedFile['table']);
				$deleted = true;
				$changeTmpDoc = false;
				break;
			}

			$_scheduleEditedDoc = false; //	shall the in webEdition edited doc be changed.
			if(isset($GLOBALS['we_doc']) && $schedFile["table"] == $GLOBALS['we_doc']->Table){ //	in webEdition bearbeitetes Dokument wird gescheduled
				$_scheduleEditedDoc = true;
			}

			switch($s["task"]){
				case self::SCHEDULE_FROM:
					$GLOBALS['we_doc']->Published = $now;
					if($_scheduleEditedDoc){
						$GLOBALS['we_doc']->Published = $now;
					}
					break;
				case self::SCHEDULE_TO:
					$GLOBALS['we_doc']->Published = 0;
					if($_scheduleEditedDoc){
						$GLOBALS['we_doc']->Published = 0;
					}
					break;
				case self::DOCTYPE:
					if($GLOBALS['we_doc']->Published){
						$publSave = $GLOBALS['we_doc']->Published;
						$GLOBALS['we_doc']->we_unpublish();
						$GLOBALS['we_doc']->DocType = $s["DoctypeID"];
						if($s["doctypeAll"]){
							$GLOBALS['we_doc']->changeDoctype($s["DoctypeID"], true);
						}
						$changeTmpDoc = true;
						$GLOBALS['we_doc']->Published = $publSave;
					}
					break;
				case self::CATEGORY:
					if($GLOBALS['we_doc']->Published){
						$GLOBALS['we_doc']->Category = $s["CategoryIDs"];
						$changeTmpDoc = true;
					}
					break;
				case self::DIR:
					if($GLOBALS['we_doc']->Published){
						$GLOBALS['we_doc']->setParentID($s["ParentID"]);
						$GLOBALS['we_doc']->Path = $GLOBALS['we_doc']->getPath();
						$changeTmpDoc = true;
					}
					break;
			}

//FIXME: why not for objectfiles????
			if($s["type"] != self::TYPE_ONCE){
				$nextWann = we_schedpro::getNextTimestamp($s, $now);
				if($nextWann){
					$DB_WE->query('UPDATE ' . SCHEDULE_TABLE . ' SET Wann=' . intval($nextWann) . ' WHERE DID=' . intval($id) . " AND ClassName!='we_objectFile' AND Type='" . $s['type'] . "' AND Was='" . $s['task'] . "'");
				}
			} else{
				$DB_WE->query('UPDATE ' . SCHEDULE_TABLE . ' SET Active=0 WHERE DID=' . intval($id) . ' AND ClassName="' . $schedFile['ClassName'] . '" AND Type="' . $s['type'] . '" AND Was="' . $s['task'] . '"');
			}
		}

		if($changeTmpDoc){
			$GLOBALS['we_doc']->we_save();
		}
		if(!$deleted){
			if($GLOBALS['we_doc']->Published){
				$GLOBALS['we_doc']->we_publish();
			} else{
				$GLOBALS['we_doc']->we_unpublish();
			}
		}

		$GLOBALS['we_doc'] = $doc_save;

		$_SESSION["Versions"]['fromScheduler'] = false;

		$DB_WE->query('UPDATE ' . SCHEDULE_TABLE . ' SET Active=0 WHERE Wann<=' . $now . ' AND Schedpro != "" AND Active=1 AND TYPE="' . self::TYPE_ONCE . '"');
	}

	static function trigger_schedule(){
		//FIXME: do we want to limit this query, if not called by cron?
		$DB_WE = new DB_WE();
		$now = time();

		while(($DB_WE->lock(array(SCHEDULE_TABLE, ERROR_LOG_TABLE)) && ($rec = getHash('SELECT * FROM ' . SCHEDULE_TABLE . ' WHERE Wann<=' . $now . ' AND lockedUntil<NOW() AND Schedpro != "" AND Active=1 ORDER BY Wann LIMIT 1', $DB_WE)))) {
			$DB_WE->query('UPDATE ' . SCHEDULE_TABLE . ' SET lockedUntil=lockedUntil+INTERVAL 1 minute WHERE DID=' . $rec['DID'] . ' AND ClassName="' . $rec['ClassName'] . '" AND Type="' . $rec["Type"] . '" AND Was="' . $rec["Was"] . '"');
			$DB_WE->unlock();
			$s = unserialize($rec["Schedpro"]);
			if(is_array($s)){
				$s["lasttime"] = we_schedpro::getPrevTimestamp($s, $now);
				$tmp = array(
					"value" => array($s),
					"ClassName" => $rec["ClassName"],
					"table" => $rec["ClassName"] == "we_objectFile" ? OBJECT_FILES_TABLE : FILE_TABLE,
				);
				we_schedpro::processSchedule($rec['DID'], $tmp, $now, $DB_WE);
			} else{
				//data invalid, reset & make sure this is not processed the next time
				$DB_WE->query('UPDATE ' . SCHEDULE_TABLE . ' SET Schedpro="" WHERE DID=' . $rec['DID'] . ' AND ClassName="' . $rec['ClassName'] . '" AND Type="' . $rec["Type"] . '" AND Was="' . $rec["Was"] . '"');
			}
		}
		//make sure DB is unlocked!
		$DB_WE->unlock();
	}

	function check_and_convert_to_sched_pro(){
		$DB_WE = NEW DB_WE();

		$scheddy = array();

		$DB_WE->query('SELECT * FROM ' . SCHEDULE_TABLE . ' WHERE Schedpro IS NULL OR Schedpro=""');
		while($DB_WE->next_record()) {
			$s = array();

			$s["did"] = $DB_WE->f("DID");
			$s["task"] = $DB_WE->f("Was");
			$s["type"] = 0;
			$s["months"] = array();
			$s["days"] = array();
			$s["weekdays"] = array();
			$s["time"] = $DB_WE->f("Wann");
			$s["CategoryIDs"] = "";
			$s["DoctypeID"] = 0;
			$s["ParentID"] = 0;
			$s["active"] = 1;
			$s["doctypeAll"] = 0;

			array_push($scheddy, $s);
		}

		foreach($scheddy as $s){
			$DB_WE->query('UPDATE ' . SCHEDULE_TABLE . ' SET Schedpro="' . $DB_WE->escape(serialize($s)) . '", Active=1, SerializedData="' . $DB_WE->escape('s:0:"";') . '" WHERE DID=' . intval($s["did"]) . " AND Was=" . intval($s["task"]) . " AND Wann=" . intval($s["time"]));
		}
	}

	function getNextTimestamp($s, $now = 0){
		if(!$now){
			$now = time();
		}
		switch($s["type"]){
			case self::TYPE_ONCE:
				return $s["time"];
			case self::TYPE_HOUR:
				$nextTime = mktime(date("G", $now), date("i", $s["time"]), 0, date("m", $now), date("j", $now), date("Y", $now));
				return ($nextTime > $now) ? $nextTime : $nextTime + 3600; // +1 h
			case self::TYPE_DAY:
				$nextTime = mktime(date("G", $s["time"]), date("i", $s["time"]), 0, date("m", $now), date("j", $now), date("Y", $now));
				return ($nextTime > $now ? $nextTime : $nextTime + 86400); // + 1 Tag
			case self::TYPE_WEEK:
				$wdayNow = date("w", $now);
				$timeSched = mktime(date("G", $s["time"]), date("i", $s["time"]), 0, intval(date("m", $now)), date("j", $now), date("Y", $now)); // zeit fuer heutigen tag
				if($s["weekdays"][$wdayNow] && ($timeSched > $now)){ // wenn am heutigen Tag was geschehen soll, checken ob Ereignis noch offen, wenn ja dann speichern
					return $timeSched;
				}
				$nextday = 0;
				$found = false;
				// naechst moeglicher Wochentag suchen
				for($wd = $wdayNow + 1; $wd <= 6; $wd++){
					$nextday++;
					if($s["weekdays"][$wd]){
						$found = true;
						break;
					}
				}
				if(!$found){
					for($wd = 0; $wd <= $wdayNow; $wd++){
						$nextday++;
						if($s["weekdays"][$wd]){
							$found = true;
							break;
						}
					}
				}
				if($found){
					$nextdaystamp = $now + ($nextday * 86400);
					return mktime(date("G", $s["time"]), date("i", $s["time"]), 0, intval(date("m", $nextdaystamp)), date("j", $nextdaystamp), date("Y", $nextdaystamp));
				}

				return 0;
			case self::TYPE_MONTH:
				$dayNow = date("j", $now);
				$timeSched = mktime(date("G", $s["time"]), date("i", $s["time"]), 0, intval(date("m", $now)), date("j", $now), date("Y", $now)); // zeit fuer heutigen tag
				if($s["days"][$dayNow - 1] && ($timeSched > $now)){ // wenn am heutigen Tag was geschehen soll, checken ob Ereignis noch offen, wenn ja dann speichern
					return $timeSched;
				}

				$tomorrow = $now + 86400;
				$dayTomorrow = date("j", $tomorrow);

				$trys = 0;
				while($s["days"][$dayTomorrow - 1] == 0 && $trys <= 365) {
					$tomorrow += 86400;
					$dayTomorrow = date("j", $tomorrow);
					$trys++;
				}
				return ($trys <= 365) ?
					mktime(date("G", $s["time"]), date("i", $s["time"]), 0, intval(date("m", $tomorrow)), date("j", $tomorrow), date("Y", $tomorrow)) :
					0;
			case self::TYPE_YEAR:
				$dayNow = date("j", $now);
				$monthNow = intval(date("m", $now));
				$timeSched = mktime(date("G", $s["time"]), date("i", $s["time"]), 0, intval(date("m", $now)), date("j", $now), date("Y", $now)); // zeit fuer heutigen tag
				if($s["days"][$dayNow - 1] && $s["months"][$monthNow - 1] && ($timeSched > $now)){ // wenn am heutigen Tag was geschehen soll, checken ob Ereignis noch offen, wenn ja dann speichern
					return $timeSched;
				}

				$tomorrow = $now + 86400;
				$dayTomorrow = date("j", $tomorrow);
				$monthTomorrow = intval(date("m", $tomorrow));

				$trys = 0;
				while(($s["days"][$dayTomorrow - 1] == 0 || $s["months"][$monthTomorrow - 1] == 0) && $trys <= 365) {
					$tomorrow += 86400;
					$dayTomorrow = date("j", $tomorrow);
					$monthTomorrow = intval(date("m", $tomorrow));
					$trys++;
				}
				return ($trys <= 365) ?
					mktime(date("G", $s["time"]), date("i", $s["time"]), 0, intval(date("m", $tomorrow)), date("j", $tomorrow), date("Y", $tomorrow)) :
					0;
		}
	}

	function getPrevTimestamp($s, $now = 0){
		if(!$now){
			$now = time();
		}
		switch($s["type"]){
			case self::TYPE_ONCE:
				return $s["time"];
			case self::TYPE_HOUR:
				$nextTime = mktime(date("G", $now), date("i", $s["time"]), 0, date("m", $now), date("j", $now), date("Y", $now));
				return ($nextTime < $now ? $nextTime : $nextTime - 3600); // +1 h
			case self::TYPE_DAY:
				$nextTime = mktime(date("G", $s["time"]), date("i", $s["time"]), 0, date("m", $now), date("j", $now), date("Y", $now));
				return ($nextTime < $now ? $nextTime : $nextTime - 86400); // + 1 Tag
			case self::TYPE_WEEK:
				$wdayNow = date("w", $now);
				$timeSched = mktime(date("G", $s["time"]), date("i", $s["time"]), 0, intval(date("m", $now)), date("j", $now), date("Y", $now)); // zeit fuer heutigen tag
				if($s["weekdays"][$wdayNow] && ($timeSched < $now)){ // wenn am heutigen Tag was geschehen soll, checken ob Ereignis noch offen, wenn ja dann speichern
					return $timeSched;
				}
				$lastday = 0;
				$found = false;
				// naechst moeglicher Wochentag suchen
				for($wd = $wdayNow - 1; $wd >= 0; $wd--){
					$lastday++;
					if($s["weekdays"][$wd]){
						$found = true;
						break;
					}
				}
				if(!$found){
					for($wd = 6; $wd >= $wdayNow; $wd--){
						$lastday++;
						if($s["weekdays"][$wd]){
							$found = true;
							break;
						}
					}
				}
				if($found){
					$lasttimestamp = $now - ($lastday * 86400);
					$timeSched = mktime(date("G", $s["time"]), date("i", $s["time"]), 0, intval(date("m", $lasttimestamp)), date("j", $lasttimestamp), date("Y", $lasttimestamp));
					return $timeSched;
				}

				return 0;
			case self::TYPE_MONTH:
				$dayNow = date("j", $now);
				$timeSched = mktime(date("G", $s["time"]), date("i", $s["time"]), 0, intval(date("m", $now)), date("j", $now), date("Y", $now)); // zeit fuer heutigen tag
				if($s["days"][$dayNow - 1] && ($timeSched < $now)){ // wenn am heutigen Tag was geschehen soll, checken ob Ereignis noch offen, wenn ja dann speichern
					return $timeSched;
				}

				$yesterday = $now - 86400;
				$dayYesterday = date("j", $yesterday);

				$trys = 0;
				while($s["days"][$dayYesterday - 1] == 0 && $trys <= 365) {
					$yesterday -= 86400;
					$dayYesterday = date("j", $yesterday);
					$trys++;
				}
				return ($trys <= 365) ?
					mktime(date("G", $s["time"]), date("i", $s["time"]), 0, intval(date("m", $yesterday)), date("j", $yesterday), date("Y", $yesterday)) :
					0;

			case self::TYPE_YEAR:
				$dayNow = date("j", $now);
				$monthNow = intval(date("m", $now));
				$timeSched = mktime(date("G", $s["time"]), date("i", $s["time"]), 0, intval(date("m", $now)), date("j", $now), date("Y", $now)); // zeit fuer heutigen tag
				if($s["days"][$dayNow - 1] && $s["months"][$monthNow - 1] && ($timeSched < $now)){ // wenn am heutigen Tag was geschehen soll, checken ob Ereignis noch offen, wenn ja dann speichern
					return $timeSched;
				}

				$yesterday = $now - 86400;
				$dayYesterday = date("j", $yesterday);
				$monthYesterday = intval(date("m", $yesterday));

				$trys = 0;
				while(($s["days"][$dayYesterday - 1] == 0 || $s["months"][$monthYesterday - 1] == 0) && $trys <= 365) {
					$yesterday -= 86400;
					$dayYesterday = date("j", $yesterday);
					$monthYesterday = intval(date("m", $yesterday));
					$trys++;
				}
				return ($trys <= 365) ?
					mktime(date("G", $s["time"]), date("i", $s["time"]), 0, intval(date("m", $yesterday)), date("j", $yesterday), date("Y", $yesterday)) :
					0;
		}
	}

}

function weCmpSchedLast($a, $b){
	if($a["lasttime"] == $b["lasttime"]){
		return 0;
	}
	return ($a["lasttime"] < $b["lasttime"]) ? -1 : 1;
}