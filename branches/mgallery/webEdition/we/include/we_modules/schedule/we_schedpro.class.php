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
we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER);

class we_schedpro{
	const SCHEDULE_FROM = 1; //publish
	const SCHEDULE_TO = 2; //park
	const DELETE = 3;
	const DOCTYPE = 4;
	const CATEGORY = 5;
	const DIR = 6;
	const SEARCHABLE_ENABLED = 7;
	const SEARCHABLE_DISABLED = 8;
	const CALL = 9;
	const TYPE_ONCE = 0;
	const TYPE_HOUR = 1;
	const TYPE_DAY = 2;
	const TYPE_WEEK = 3;
	const TYPE_MONTH = 4;
	const TYPE_YEAR = 5;

	var $task = self::SCHEDULE_FROM;
	var $type = self::TYPE_ONCE;
	var $months = array();
	var $days = array();
	var $weekdays = array();
	var $time = 0;
	var $nr = 0;
	var $CategoryIDs = '';
	var $DoctypeID = 0;
	var $ParentID = 0;
	var $active = 1;
	var $doctypeAll = 0;

	function __construct($s = '', $nr = 0){
		if(is_array($s)){
			$this->task = isset($s['task']) ? $s['task'] : $this->task;
			$this->type = isset($s['type']) ? $s['type'] : $this->type;
			$this->months = isset($s['months']) ? $s['months'] : $this->months;
			$this->days = isset($s['days']) ? $s['days'] : $this->days;
			$this->weekdays = isset($s['weekdays']) ? $s['weekdays'] : $this->weekdays;
			$this->time = isset($s['time']) ? $s['time'] : time();
			$this->CategoryIDs = isset($s['CategoryIDs']) ? $s['CategoryIDs'] : $this->CategoryIDs;
			$this->DoctypeID = isset($s['DoctypeID']) ? $s['DoctypeID'] : $this->DoctypeID;
			$this->ParentID = isset($s['ParentID']) ? $s['ParentID'] : $this->ParentID;
			$this->active = isset($s['active']) ? $s['active'] : $this->active;
			$this->doctypeAll = isset($s['doctypeAll']) ? $s['doctypeAll'] : $this->doctypeAll;
		} else {
			$this->time = time();
		}
		$this->nr = intval($nr);
	}

	function getMonthsHTML(){
		$months = '<table class="default"><tr>';

		for($i = 1; $i <= 12; $i++){
			$months .= '<td>' . we_html_forms::checkbox(1, $this->months[$i - 1], "check_we_schedule_month" . $i . "_" . $this->nr, g_l('date', '[month][short][' . ($i - 1) . ']'), false, "defaultfont", "this.form.elements['we_schedule_month" . $i . "_" . $this->nr . "'].value=this.checked?1:0;_EditorFrame.setEditorIsHot(true)") .
				we_html_element::htmlHidden('we_schedule_month' . $i . '_' . $this->nr, $this->months[$i - 1]) . '</td>';
		}

		$months .= '</tr></table>';
		return $months;
	}

	function getDaysHTML(){
		$days = '<table class="default"><tr>';

		for($i = 1; $i <= 36; $i++){
			if($i <= 31){
				$days .= '<td>' . we_html_forms::checkbox(1, $this->days[$i - 1], "check_we_schedule_day" . $i . "_" . $this->nr, sprintf('%02d', $i), false, "defaultfont", "this.form.elements['we_schedule_day" . $i . "_" . $this->nr . "'].value=this.checked?1:0;_EditorFrame.setEditorIsHot(true)") .
					'<input type="hidden" name="we_schedule_day' . $i . '_' . $this->nr . '" value="' . $this->days[$i - 1] . '" /></td><td class="defaultfont">&nbsp;</td>';
			} else {
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
		$wd = '<table class="default"><tr>';

		for($i = 1; $i <= 7; $i++){
			$wd .= '<td>' . we_html_forms::checkbox(1, $this->weekdays[$i - 1], "check_we_schedule_wday'.$i.'_'.$this->nr.'", g_l('date', '[day][short][' . ($i - 1) . ']'), false, "defaultfont", "this.form.elements['we_schedule_wday" . $i . "_" . $this->nr . "'].value=this.checked?1:0;_EditorFrame.setEditorIsHot(true)") .
				'<input type="hidden" name="we_schedule_wday' . $i . '_' . $this->nr . '" value="' . $this->weekdays[$i - 1] . '" /></td><td class="defaultfont">&nbsp;</td>';
		}

		$wd .= '</tr></table>';
		return $wd;
	}

	function getSpacerRowHTML(){
		return '
<tr valign="top">
	<td>' . we_html_tools::getPixel(80, 10) . '</td>
	<td>' . we_html_tools::getPixel(565, 10) . '</td>
	<td>' . we_html_tools::getPixel(26, 10) . '</td>
</tr>';
	}

	//needed to switch description of button publish to "save to scheduler" and vice versa
	public static function getMainJS($doc){
		return we_html_element::jsElement('
function checkFooter(){
	var button=parent.editFooter.document.getElementById("publish_' . $doc->ID . '")
	var aEl=this.document.getElementsByClassName("we_schedule_active");
	var active=false;
	if(button != undefined){
	button=button.getElementsByTagName("button")[0];
		for( var i=0; i<aEl.length; ++i){
			if(aEl[i].value==1){
			var no=aEl[i].name.split("we_schedule_active_");
			if(this.document.getElementsByName("we_schedule_task_"+no[1])[0].value== ' . self::SCHEDULE_FROM . '){
				active=true;
				break;
			}
		}
	}

	if(active){
		button.title="' . g_l('button', '[saveInScheduler][alt]') . '";
		button.style.width="' . g_l('button', '[saveInScheduler][width]') . 'px";
		button.innerHTML="<i class=\"fa fa-lg fa-clock-o\"></i> ' . g_l('button', '[saveInScheduler][value]') . '";
	}else{
		button.title="' . g_l('button', '[publish][alt]') . '";
		button.style.width="' . g_l('button', '[publish][width]') . 'px";
		button.innerHTML="<i class=\"fa fa-lg fa-sun-o\"></i> ' . g_l('button', '[publish][value]') . '";
	}
}
//we_schedule_task
}
');
	}

	function getHTML($isobj = false){
		$taskpopup = '<select class="weSelect we_schedule_task" name="we_schedule_task_' . $this->nr . '" size="1" onchange="_EditorFrame.setEditorIsHot(true);checkFooter();if(self.we_hasExtraRow_' . $this->nr . ' || this.options[this.selectedIndex].value==' . self::DOCTYPE . ' || this.options[this.selectedIndex].value==' . self::CATEGORY . ' || this.options[this.selectedIndex].value==' . self::DIR . '){ setScrollTo();we_cmd(\'reload_editpage\');}">
<option value="' . self::SCHEDULE_FROM . '"' . (($this->task == self::SCHEDULE_FROM) ? ' selected' : '') . '>' . g_l('modules_schedule', "[task][" . self::SCHEDULE_FROM . ']') . '</option>
<option value="' . self::SCHEDULE_TO . '"' . (($this->task == self::SCHEDULE_TO) ? ' selected' : '') . '>' . g_l('modules_schedule', '[task][' . self::SCHEDULE_TO . ']') . '</option>';
		if((permissionhandler::hasPerm('DELETE_DOCUMENT') && (!$isobj)) || (permissionhandler::hasPerm('DELETE_OBJECTFILE') && $isobj)){
			$taskpopup .= '<option value="' . self::DELETE . '"' . (($this->task == self::DELETE) ? ' selected' : '') . '>' . g_l('modules_schedule', '[task][' . self::DELETE . ']') . '</option>';
		}
		if(!$isobj){
			$taskpopup .= '<option value="' . self::DOCTYPE . '"' . (($this->task == self::DOCTYPE) ? ' selected' : '') . '>' . g_l('modules_schedule', '[task][' . self::DOCTYPE . ']') . '</option>
<option value="' . self::CALL . '"' . (($this->task == self::CALL) ? ' selected' : '') . '>' . g_l('modules_schedule', '[task][' . self::CALL . ']') . '</option>';
		}
		$taskpopup .= '<option value="' . self::CATEGORY . '"' . (($this->task == self::CATEGORY) ? ' selected' : '') . '>' . g_l('modules_schedule', '[task][' . self::CATEGORY . ']') . '</option>';
		if((permissionhandler::hasPerm('MOVE_DOCUMENT') && (!$isobj)) || (permissionhandler::hasPerm("MOVE_OBJECTFILE") && $isobj)){
			$taskpopup .= '<option value="' . self::DIR . '"' . (($this->task == self::DIR) ? ' selected' : '') . '>' . g_l('modules_schedule', '[task][' . self::DIR . ']') . '</option>';
		}
		$taskpopup .= '
<option value="' . self::SEARCHABLE_ENABLED . '"' . (($this->task == self::SEARCHABLE_ENABLED) ? ' selected' : '') . '>' . g_l('modules_schedule', '[task][' . self::SEARCHABLE_ENABLED . ']') . '</option>
<option value="' . self::SEARCHABLE_DISABLED . '"' . (($this->task == self::SEARCHABLE_DISABLED) ? ' selected' : '') . '>' . g_l('modules_schedule', '[task][' . self::SEARCHABLE_DISABLED . ']') . '</option>
</select>';
		$extracont = '';
		$extraheadl = '';


		switch($this->task){
			case self::DOCTYPE:
				$db = new DB_WE();
				$dtq = we_docTypes::getDoctypeQuery($db);
				$db->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN tblFile dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where']);
				$doctypepop = '<select class="weSelect" name="we_schedule_doctype_' . $this->nr . '" size="1" onchange="_EditorFrame.setEditorIsHot(true)">';
				while($db->next_record()){
					$doctypepop .= '<option value="' . $db->f("ID") . '"' . (($this->DoctypeID == $db->f("ID")) ? ' selected="selected"' : '') . '>' . $db->f("DocType") . '</option>';
				}
				$doctypepop .= '</select>';
				$checknname = md5(uniqid(__FUNCTION__, true));
				$extracont = '<table class="default"><tr><td>' . $doctypepop . '</td><td class="defaultfont">&nbsp;&nbsp;</td><td>' . we_html_forms::checkbox(1, $this->doctypeAll, $checknname, g_l('modules_schedule', '[doctypeAll]')
						, false, "defaultfont", "this.form.elements['we_schedule_doctypeAll_" . $this->nr . "'].value=this.checked?1:0;") .
					'<input type="hidden" name="we_schedule_doctypeAll_' . $this->nr . '" value="' . $this->doctypeAll . '" /></td></tr></table>';
				$extraheadl = g_l('modules_schedule', '[doctype]');
				break;
			case self::CATEGORY:
				$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:we_cmd('schedule_delete_all_schedcats'," . $this->nr . ")");
				$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_category',-1,'" . CATEGORY_TABLE . "','','','opener.setScrollTo();opener.top.we_cmd(\\'schedule_add_schedcat\\',top.currentID," . $this->nr . ");')");
				$cats = new we_chooser_multiDir(450, $this->CategoryIDs, "schedule_delete_schedcat", we_html_button::create_button_table(array($delallbut, $addbut)), "", 'IF(IsFolder,"folder","we/category")', CATEGORY_TABLE, "defaultfont", $this->nr);
				$cats->extraDelFn = 'setScrollTo();';
				if(!permissionhandler::hasPerm("EDIT_KATEGORIE")){
					$cats->isEditable = false;
				}
				$extracont = $cats->get();
				$extraheadl = g_l('modules_schedule', '[categories]');
				break;
			case self::DIR:
				$textname = 'path_we_schedule_parentid_' . $this->nr;
				$idname = 'we_schedule_parentid_' . $this->nr;
				$myid = $this->ParentID;
				$path = id_to_path($this->ParentID, $GLOBALS['we_doc']->Table);

				if($GLOBALS['we_doc'] instanceof we_objectFile){
					if($path === '/'){ //	impossible for documents
						$path = $GLOBALS['we_doc']->RootDirPath;
					}
					$_rootDirID = $GLOBALS['we_doc']->rootDirID;
				} else {
					$_rootDirID = 0;
				}

				$wecmdenc1 = we_base_request::encCmd('document.we_form.elements[\'' . $idname . '\'].value');
				$wecmdenc2 = we_base_request::encCmd('document.we_form.elements[\'' . $textname . '\'].value');
				$wecmdenc3 = we_base_request::encCmd('top.opener._EditorFrame.setEditorIsHot(true);');
				$button = we_html_button::create_button(we_html_button::SELECT, 'javascript:we_cmd(\'we_selector_directory\',document.we_form.elements[\'' . $idname . '\'].value,\'' . $GLOBALS['we_doc']->Table . '\',\'' . $wecmdenc1 . '\',\'' . $wecmdenc2 . '\',\'' . $wecmdenc3 . '\',\'\',\'' . $_rootDirID . '\')');

				$yuiSuggest = & weSuggest::getInstance();
				$yuiSuggest->setAcId('WsDir');
				$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER);
				$yuiSuggest->setInput($textname, $path);
				$yuiSuggest->setMaxResults(20);
				$yuiSuggest->setMayBeEmpty(0);
				$yuiSuggest->setResult($idname, $myid);
				$yuiSuggest->setSelector(weSuggest::DirSelector);
				$yuiSuggest->setTable(FILE_TABLE);
				$yuiSuggest->setWidth(320);
				$yuiSuggest->setSelectButton($button);

				$extracont = weSuggest::getYuiFiles() . $yuiSuggest->getHTML() . $yuiSuggest->getYuiJs();
				$extraheadl = g_l('modules_schedule', '[dirctory]');
		}

		$typepopup = '<select class="weSelect" name="we_schedule_type_' . $this->nr . '" size="1" onchange="_EditorFrame.setEditorIsHot(true);setScrollTo();we_cmd(\'reload_editpage\')">
<option value="' . self::TYPE_ONCE . '"' . (($this->type == self::TYPE_ONCE) ? ' selected' : '') . '>' . g_l('modules_schedule', '[type][' . self::TYPE_ONCE . ']') . '</option>
<option value="' . self::TYPE_HOUR . '"' . (($this->type == self::TYPE_HOUR) ? ' selected' : '') . '>' . g_l('modules_schedule', '[type][' . self::TYPE_HOUR . ']') . '</option>
<option value="' . self::TYPE_DAY . '"' . (($this->type == self::TYPE_DAY) ? ' selected' : '') . '>' . g_l('modules_schedule', '[type][' . self::TYPE_DAY . ']') . '</option>
<option value="' . self::TYPE_WEEK . '"' . (($this->type == self::TYPE_WEEK) ? ' selected' : '') . '>' . g_l('modules_schedule', '[type][' . self::TYPE_WEEK . ']') . '</option>
<option value="' . self::TYPE_MONTH . '"' . (($this->type == self::TYPE_MONTH) ? ' selected' : '') . '>' . g_l('modules_schedule', '[type][' . self::TYPE_MONTH . ']') . '</option>
<option value="' . self::TYPE_YEAR . '"' . (($this->type == self::TYPE_YEAR) ? ' selected' : '') . '>' . g_l('modules_schedule', '[type][' . self::TYPE_YEAR . ']') . '</option>
</select>';


		$checknname = md5(uniqid(__FUNCTION__, true));
		$table = '<table class="default">
	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', '[task][headline]') . ':</td>
		<td class="defaultfont"><table class="default"><tr><td>' . $taskpopup . '</td><td class="defaultfont">&nbsp;&nbsp;</td><td>' . we_html_forms::checkbox(1, $this->active, $checknname, g_l('modules_schedule', '[active]')
				, false, "defaultfont", "this.form.elements['we_schedule_active_" . $this->nr . "'].value=this.checked?1:0;_EditorFrame.setEditorIsHot(true);checkFooter();") .
			'<input type="hidden" class="we_schedule_active" name="we_schedule_active_' . $this->nr . '" value="' . $this->active . '" /></td></tr></table></td>
		<td>' . we_html_button::create_button(we_html_button::TRASH, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('schedule_del','" . $this->nr . "')") . '</td>
	</tr>' . $this->getSpacerRowHTML();
		if($extracont){
			$table .= '
	<tr valign="top">
		<td class="defaultgray">' . $extraheadl . ':</td>
		<td class="defaultfont">' . $extracont . '</td>
		<td></td>
	</tr>' . $this->getSpacerRowHTML();
		}

		$table .= '
	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', '[type][headline]') . ':</td>
		<td class="defaultfont">' . $typepopup . '</td>
		<td></td>
	</tr>' .
			$this->getSpacerRowHTML();


		switch($this->type){
			case self::TYPE_ONCE:
				$table .= '
	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', '[datetime]') . ':</td>
		<td class="defaultfont">' . we_html_tools::getDateInput2("we_schedule_time%s_" . $this->nr, $this->time, true) . '</td>
		<td></td>
	</tr>';
				break;
			case self::TYPE_HOUR:
				$table .= '
	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', '[minutes]') . ':</td>
		<td class="defaultfont">' . we_html_tools::getDateInput2("we_schedule_time%s_" . $this->nr, $this->time, true, "i") . '</td>
		<td></td>
	</tr>';
				break;
			case self::TYPE_DAY:
				$table .= '
	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', '[time]') . ':</td>
		<td class="defaultfont">' . we_html_tools::getDateInput2("we_schedule_time%s_" . $this->nr, $this->time, true, "h:i") . '</td>
		<td></td>
	</tr>';
				break;
			case self::TYPE_WEEK:
				$table .= '
	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', '[time]') . ':</td>
		<td class="defaultfont">' . we_html_tools::getDateInput2("we_schedule_time%s_" . $this->nr, $this->time, true, "h:i") . '</td>
		<td></td>
	</tr>' .
					$this->getSpacerRowHTML() . '
	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', '[weekdays]') . ':</td>
		<td class="defaultfont">' . $this->getWeekdaysHTML() . '</td>
		<td></td>
	</tr>';
				break;
			case self::TYPE_MONTH:
				$table .= '
	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', '[time]') . ':</td>
		<td class="defaultfont">' . we_html_tools::getDateInput2("we_schedule_time%s_" . $this->nr, $this->time, true, "h:i") . '</td>
		<td></td>
	</tr>' .
					$this->getSpacerRowHTML() . '
	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', '[days]') . ':</td>
		<td class="defaultfont">' . $this->getDaysHTML() . '</td>
		<td></td>
	</tr>';
				break;
			case self::TYPE_YEAR:
				$table .= '
	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', '[time]') . ':</td>
		<td class="defaultfont">' . we_html_tools::getDateInput2("we_schedule_time%s_" . $this->nr, $this->time, true, "h:i") . '</td>
		<td></td>
	</tr>' .
					$this->getSpacerRowHTML() . '
	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', '[months]') . ':</td>
		<td class="defaultfont">' . $this->getMonthsHTML() . '</td>
		<td></td>
	</tr>' .
					$this->getSpacerRowHTML() . '
	<tr valign="top">
		<td class="defaultgray">' . g_l('modules_schedule', '[days]') . ':</td>
		<td class="defaultfont">' . $this->getDaysHTML() . '</td>
		<td></td>
	</tr>';
				break;
		}
		$table .= '</table>';
		return we_html_element::jsElement('var we_hasExtraRow_' . intval($this->nr) . '=' . ($extracont ? 'true' : 'false')) . $table;
	}

	function processSchedule($id, $schedFile, $now, we_database_base $DB_WE){
		usort($schedFile['value'], array('we_schedpro', 'weCmpSchedLast'));
		$GLOBALS['we']['Scheduler_active'] = 1;
		$doc_save = isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc'] : NULL;
		$GLOBALS['we_doc'] = new $schedFile['ClassName']();
		$GLOBALS['we_doc']->InitByID($id, $schedFile["table"], we_class::LOAD_SCHEDULE_DB);
		$callPublish = true;
		$changeTmpDoc = false;
		$_SESSION['weS']['versions']['fromScheduler'] = true;

		foreach($schedFile['value'] as $s){
			switch($s['task']){
				case self::DELETE:
					we_base_delete::deleteEntry($id, $schedFile['table']);
					$callPublish = false;
					$changeTmpDoc = false;
					break 2; //exit foreach

				case self::SCHEDULE_FROM:
					$GLOBALS['we_doc']->Published = $now;
					break;
				case self::SCHEDULE_TO:
					$GLOBALS['we_doc']->Published = 0;
					break;
				case self::DOCTYPE:
					$publSave = $GLOBALS['we_doc']->Published;
					if($GLOBALS['we_doc']->Published){
						$GLOBALS['we_doc']->we_unpublish();
					}
					$GLOBALS['we_doc']->DocType = $s['DoctypeID'];
					if($s['doctypeAll']){
						$GLOBALS['we_doc']->changeDoctype($s['DoctypeID'], true);
					}
					$changeTmpDoc = true;
					$GLOBALS['we_doc']->Published = $publSave;
					break;
				case self::CATEGORY:
					$GLOBALS['we_doc']->Category = $s['CategoryIDs'];
					$changeTmpDoc = true;
					break;
				case self::DIR:
					$GLOBALS['we_doc']->setParentID($s['ParentID']);
					$GLOBALS['we_doc']->Path = $GLOBALS['we_doc']->getPath();
					$changeTmpDoc = true;
					break;
				case self::SEARCHABLE_ENABLED:
					$GLOBALS['we_doc']->IsSearchable = true;
					$changeTmpDoc = true;
					break;
				case self::SEARCHABLE_DISABLED:
					$GLOBALS['we_doc']->IsSearchable = false;
					$changeTmpDoc = true;
					break;
				case self::CALL:
					require_once(WE_INCLUDES_PATH . 'we_tag.inc.php');
					$callPublish = (count($schedFile['value']) > 1); //only if other operations pending
					//don't show any output
					ob_start();
					//we can't use include-tag since we don't have a document & many warnings will occur.
					$path = id_to_path($id, FILE_TABLE, $DB_WE);
					if($path){
						include($_SERVER['DOCUMENT_ROOT'] . $path);
					}
					ob_end_clean();
					break;
			}

			if($s['type'] != self::TYPE_ONCE && ($nextWann = self::getNextTimestamp($s, $now))){
				$DB_WE->query('UPDATE ' . SCHEDULE_TABLE . ' SET Wann=' . intval($nextWann) . ' WHERE Active=1 AND DID=' . intval($id) . ' AND ClassName="' . $schedFile['ClassName'] . '" AND Type="' . $s['type'] . '" AND Was="' . $s['task'] . '" AND Wann=' . $schedFile['Wann']);
			} else {
				$DB_WE->query('UPDATE ' . SCHEDULE_TABLE . ' SET Active=0 WHERE Active=1 AND DID=' . intval($id) . ' AND ClassName="' . $schedFile['ClassName'] . '" AND Type="' . $s['type'] . '" AND Was="' . $s['task'] . '" AND Wann=' . $schedFile['Wann']);
			}
		}

		if($changeTmpDoc){
			if(!$GLOBALS['we_doc']->we_save()){
				t_e('while scheduled save of document');
			}
		}

		if($callPublish){
			$pub = ($GLOBALS['we_doc']->Published ?
					$GLOBALS['we_doc']->we_publish() :
					$GLOBALS['we_doc']->we_unpublish());

			if(!$pub){
				t_e('Error while scheduled publish/unpublish of document', $GLOBALS['we_doc']->getErrMsg(), $GLOBALS['we_doc']);
			}
		}

		$GLOBALS['we_doc'] = $doc_save;

		$_SESSION['weS']['versions']['fromScheduler'] = false;

		//		$DB_WE->query('UPDATE ' . SCHEDULE_TABLE . ' SET Active=0 WHERE DID=' . intval($id) . ' AND Wann<=' . $now . ' AND Schedpro != "" AND Active=1 AND Type="' . self::TYPE_ONCE . '"');
		unset($GLOBALS['we']['Scheduler_active']);
	}

	static function trigger_schedule(){
		$DB_WE = new DB_WE();
		$now = time();
		$hasLock = $DB_WE->hasLock();
		//allow at max 10 scheduled activities per call, in case no cron is used.
		$maxSched = defined('SCHEDULED_BY_CRON') ? -1 : 10;
//make sure documents don't know they are inside WE
		if(isset($GLOBALS['WE_MAIN_EDITMODE']) || isset($GLOBALS['we_editmode'])){
			$lastWEState = array(
				'WE_MAIN_EDITMODE' => (isset($GLOBALS['WE_MAIN_EDITMODE']) ? $GLOBALS['WE_MAIN_EDITMODE'] : $GLOBALS['we_editmode']),
				'we_editmode' => isset($GLOBALS['we_editmode']) ? $GLOBALS['we_editmode'] : 0,
			);
			$GLOBALS['WE_MAIN_EDITMODE'] = $GLOBALS['we_editmode'] = false;
		}

		while((!$hasLock || $DB_WE->lock(array(SCHEDULE_TABLE, ERROR_LOG_TABLE))) && ( --$maxSched != 0) && ($rec = getHash('SELECT * FROM ' . SCHEDULE_TABLE . ' WHERE Wann<=UNIX_TIMESTAMP() AND lockedUntil<NOW() AND Active=1 ORDER BY Wann LIMIT 1', $DB_WE))){
			$DB_WE->query('UPDATE ' . SCHEDULE_TABLE . ' SET lockedUntil=lockedUntil+INTERVAL 1 minute WHERE DID=' . $rec['DID'] . ' AND Active=1 AND ClassName="' . $rec['ClassName'] . '" AND Type="' . $rec["Type"] . '" AND Was="' . $rec["Was"] . '" AND Wann=' . $rec['Wann']);
			if($hasLock){
				$DB_WE->unlock();
			}
			$s = we_unserialize($rec['Schedpro']);
			if(is_array($s)){
				$s['lasttime'] = self::getPrevTimestamp($s, $now);
				$tmp = array(
					'value' => array($s),
					'ClassName' => $rec['ClassName'],
					'Wann' => $rec['Wann'],
					'table' => $rec['ClassName'] === 'we_objectFile' ? OBJECT_FILES_TABLE : FILE_TABLE,
				);
				self::processSchedule($rec['DID'], $tmp, $now, $DB_WE);
			} else {
				//data invalid, reset & make sure this is not processed the next time
				$DB_WE->query('UPDATE ' . SCHEDULE_TABLE . ' SET Active=0, Schedpro="' . serialize(array()) . '" WHERE DID=' . $rec['DID'] . ' AND Active=1 AND Wann=' . $rec['Wann'] . ' AND ClassName="' . $rec['ClassName'] . '" AND Type="' . $rec["Type"] . '" AND Was="' . $rec["Was"] . '"');
			}
		}
		//make sure DB is unlocked!
		$DB_WE->unlock();
//reset state
		if(isset($lastWEState)){
			$GLOBALS['WE_MAIN_EDITMODE'] = $lastWEState['WE_MAIN_EDITMODE'];
			$GLOBALS['we_editmode'] = $lastWEState['we_editmode'];
		}
	}

	function getNextTimestamp($s, $now = 0){
		if(!$now){
			$now = time();
		}
		switch($s['type']){
			case self::TYPE_ONCE:
				return $s['time'];
			case self::TYPE_HOUR:
				$nextTime = mktime(date('G', $now), date('i', $s['time']), 0, date('m', $now), date('j', $now), date('Y', $now));
				return ($nextTime > $now) ? $nextTime : $nextTime + 3600; // +1 h
			case self::TYPE_DAY:
				$nextTime = mktime(date('G', $s['time']), date('i', $s['time']), 0, date('m', $now), date('j', $now), date('Y', $now));
				return ($nextTime > $now ? $nextTime : $nextTime + 86400); // + 1 Tag
			case self::TYPE_WEEK:
				$wdayNow = date('w', $now);
				$timeSched = mktime(date('G', $s['time']), date('i', $s['time']), 0, intval(date('m', $now)), date('j', $now), date('Y', $now)); // zeit fuer heutigen tag
				if($s['weekdays'][$wdayNow] && ($timeSched > $now)){ // wenn am heutigen Tag was geschehen soll, checken ob Ereignis noch offen, wenn ja dann speichern
					return $timeSched;
				}
				$nextday = 0;
				$found = false;
				// naechst moeglicher Wochentag suchen
				for($wd = $wdayNow + 1; $wd <= 6; $wd++){
					$nextday++;
					if($s['weekdays'][$wd]){
						$found = true;
						break;
					}
				}
				if(!$found){
					for($wd = 0; $wd <= $wdayNow; $wd++){
						$nextday++;
						if($s['weekdays'][$wd]){
							$found = true;
							break;
						}
					}
				}
				if($found){
					$nextdaystamp = $now + ($nextday * 86400);
					return mktime(date('G', $s['time']), date('i', $s['time']), 0, intval(date('m', $nextdaystamp)), date('j', $nextdaystamp), date('Y', $nextdaystamp));
				}

				return 0;
			case self::TYPE_MONTH:
				$dayNow = date('j', $now);
				$timeSched = mktime(date('G', $s['time']), date('i', $s['time']), 0, intval(date('m', $now)), date('j', $now), date('Y', $now)); // zeit fuer heutigen tag
				if($s['days'][$dayNow - 1] && ($timeSched > $now)){ // wenn am heutigen Tag was geschehen soll, checken ob Ereignis noch offen, wenn ja dann speichern
					return $timeSched;
				}

				$tomorrow = $now + 86400;
				$dayTomorrow = date('j', $tomorrow);

				$trys = 0;
				while($s['days'][$dayTomorrow - 1] == 0 && $trys <= 365){
					$tomorrow += 86400;
					$dayTomorrow = date('j', $tomorrow);
					$trys++;
				}
				return ($trys <= 365) ?
					mktime(date('G', $s['time']), date('i', $s['time']), 0, intval(date('m', $tomorrow)), date('j', $tomorrow), date('Y', $tomorrow)) :
					0;
			case self::TYPE_YEAR:
				$dayNow = date('j', $now);
				$monthNow = intval(date('m', $now));
				$timeSched = mktime(date('G', $s['time']), date('i', $s['time']), 0, intval(date('m', $now)), date('j', $now), date('Y', $now)); // zeit fuer heutigen tag
				if($s['days'][$dayNow - 1] && $s['months'][$monthNow - 1] && ($timeSched > $now)){ // wenn am heutigen Tag was geschehen soll, checken ob Ereignis noch offen, wenn ja dann speichern
					return $timeSched;
				}

				$tomorrow = $now + 86400;
				$dayTomorrow = date('j', $tomorrow);
				$monthTomorrow = intval(date('m', $tomorrow));

				$trys = 0;
				while(($s['days'][$dayTomorrow - 1] == 0 || $s['months'][$monthTomorrow - 1] == 0) && $trys <= 365){
					$tomorrow += 86400;
					$dayTomorrow = date('j', $tomorrow);
					$monthTomorrow = intval(date('m', $tomorrow));
					$trys++;
				}
				return ($trys <= 365) ?
					mktime(date('G', $s['time']), date('i', $s['time']), 0, intval(date('m', $tomorrow)), date('j', $tomorrow), date('Y', $tomorrow)) :
					0;
		}
	}

	static function getEmptyEntry(){
		return array(
			'task' => 1,
			'type' => 0,
			'months' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
			'days' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
			'weekdays' => array(0, 0, 0, 0, 0, 0, 0),
			'time' => time(),
			'CategoryIDs' => '',
			'DoctypeID' => 0,
			'ParentID' => 0,
			'active' => 1,
			'doctypeAll' => 0,
		);
	}

	function getPrevTimestamp($s, $now = 0){
		if(!$now){
			$now = time();
		}
		switch($s['type']){
			case self::TYPE_ONCE:
				return $s['time'];
			case self::TYPE_HOUR:
				$nextTime = mktime(date('G', $now), date('i', $s['time']), 0, date('m', $now), date('j', $now), date('Y', $now));
				return ($nextTime < $now ? $nextTime : $nextTime - 3600); // +1 h
			case self::TYPE_DAY:
				$nextTime = mktime(date('G', $s['time']), date('i', $s['time']), 0, date('m', $now), date('j', $now), date('Y', $now));
				return ($nextTime < $now ? $nextTime : $nextTime - 86400); // + 1 Tag
			case self::TYPE_WEEK:
				$wdayNow = date('w', $now);
				$timeSched = mktime(date('G', $s['time']), date('i', $s['time']), 0, intval(date('m', $now)), date('j', $now), date('Y', $now)); // zeit fuer heutigen tag
				if($s['weekdays'][$wdayNow] && ($timeSched < $now)){ // wenn am heutigen Tag was geschehen soll, checken ob Ereignis noch offen, wenn ja dann speichern
					return $timeSched;
				}
				$lastday = 0;
				$found = false;
				// naechst moeglicher Wochentag suchen
				for($wd = $wdayNow - 1; $wd >= 0; $wd--){
					$lastday++;
					if($s['weekdays'][$wd]){
						$found = true;
						break;
					}
				}
				if(!$found){
					for($wd = 6; $wd >= $wdayNow; $wd--){
						$lastday++;
						if($s['weekdays'][$wd]){
							$found = true;
							break;
						}
					}
				}
				if($found){
					$lasttimestamp = $now - ($lastday * 86400);
					$timeSched = mktime(date('G', $s['time']), date('i', $s['time']), 0, intval(date('m', $lasttimestamp)), date('j', $lasttimestamp), date('Y', $lasttimestamp));
					return $timeSched;
				}

				return 0;
			case self::TYPE_MONTH:
				$dayNow = date('j', $now);
				$timeSched = mktime(date('G', $s['time']), date('i', $s['time']), 0, intval(date('m', $now)), date('j', $now), date('Y', $now)); // zeit fuer heutigen tag
				if($s['days'][$dayNow - 1] && ($timeSched < $now)){ // wenn am heutigen Tag was geschehen soll, checken ob Ereignis noch offen, wenn ja dann speichern
					return $timeSched;
				}

				$yesterday = $now - 86400;
				$dayYesterday = date('j', $yesterday);

				$trys = 0;
				while($s['days'][$dayYesterday - 1] == 0 && $trys <= 365){
					$yesterday -= 86400;
					$dayYesterday = date('j', $yesterday);
					$trys++;
				}
				return ($trys <= 365) ?
					mktime(date('G', $s['time']), date('i', $s['time']), 0, intval(date('m', $yesterday)), date('j', $yesterday), date('Y', $yesterday)) :
					0;

			case self::TYPE_YEAR:
				$dayNow = date('j', $now);
				$monthNow = intval(date('m', $now));
				$timeSched = mktime(date('G', $s['time']), date('i', $s['time']), 0, intval(date('m', $now)), date('j', $now), date('Y', $now)); // zeit fuer heutigen tag
				if($s['days'][$dayNow - 1] && $s['months'][$monthNow - 1] && ($timeSched < $now)){ // wenn am heutigen Tag was geschehen soll, checken ob Ereignis noch offen, wenn ja dann speichern
					return $timeSched;
				}

				$yesterday = $now - 86400;
				$dayYesterday = date('j', $yesterday);
				$monthYesterday = intval(date('m', $yesterday));

				$trys = 0;
				while(($s['days'][$dayYesterday - 1] == 0 || $s['months'][$monthYesterday - 1] == 0) && $trys <= 365){
					$yesterday -= 86400;
					$dayYesterday = date('j', $yesterday);
					$monthYesterday = intval(date('m', $yesterday));
					$trys++;
				}
				return ($trys <= 365) ?
					mktime(date('G', $s['time']), date('i', $s['time']), 0, intval(date('m', $yesterday)), date('j', $yesterday), date('Y', $yesterday)) :
					0;
		}
	}

	static function saveInScheduler($object){
		if(!isset($object->schedArr)){
			return false;
		}
		foreach($object->schedArr as $s){
			if($s['task'] == self::SCHEDULE_FROM && $s['active']){
				return true;
			}
		}
		return false;
	}

	static function publInScheduleTable($object, we_database_base $db = null){
		$db = $db ? : new DB_WE();
		$db->query('DELETE FROM ' . SCHEDULE_TABLE . ' WHERE DID=' . intval($object->ID) . ' AND ClassName="' . $db->escape($object->ClassName) . '"');
		$makeSched = false;
		foreach($object->schedArr as $s){
			if($s['task'] == self::SCHEDULE_FROM && $s['active']){
				$serializedDoc = serialize(we_temporaryDocument::load($object->ID, $object->Table, $db));
				$makeSched = true;
			} else {
				$serializedDoc = '';
			}
			$Wann = self::getNextTimestamp($s, time());

			if(!$db->query('INSERT INTO ' . SCHEDULE_TABLE . ' SET ' . we_database_base::arraySetter(array(
						'DID' => $object->ID,
						'Wann' => $Wann,
						'Was' => $s['task'],
						'ClassName' => $object->ClassName,
						'SerializedData' => ($serializedDoc ? sql_function('x\'' . bin2hex(gzcompress($serializedDoc, 9)) . '\'') : ''),
						'Schedpro' => serialize($s),
						'Type' => $s['type'],
						'Active' => $s['active']
				)))){
				return false;
			}
		}
		return $makeSched;
	}

	private static function weCmpSchedLast($a, $b){
		if($a['lasttime'] == $b['lasttime']){
			return 0;
		}
		return ($a['lasttime'] < $b['lasttime']) ? -1 : 1;
	}

}
