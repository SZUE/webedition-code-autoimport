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
/* the parent class of storagable webEdition classes */
we_base_moduleInfo::isActive(we_base_moduleInfo::EXPORT);

class we_export_view extends we_modules_view{

	var $export;
	var $editorBodyFrame;
	var $editorBodyDoc;
	var $editorBodyForm;
	private $page;

	public function __construct($frameset = "", $topframe = "top.content"){
		parent::__construct($frameset, $topframe);
		$this->export = new we_export_export();
	}

	function setTopFrame($frame){
		parent::setTopFrame($frame);
		$this->editorBodyFrame = $frame . '.editor.edbody';
		$this->editorBodyForm = $this->editorBodyFrame . '.document.we_form';
		$this->editorHeaderFrame = $frame . '.editor.edheader';
	}

	//------------------------------------------------


	function getCommonHiddens($cmds = array()){
		return
				parent::getCommonHiddens($cmds) .
				$this->htmlHidden("table", we_base_request::_(we_base_request::TABLE, "table", FILE_TABLE)) .
				$this->htmlHidden("ID", (isset($this->export->ID) ? $this->export->ID : 0)) .
				$this->htmlHidden("IsFolder", (isset($this->export->IsFolder) ? $this->export->IsFolder : 0)) .
				$this->htmlHidden("selDocs", (isset($this->export->selDocs) ? $this->export->selDocs : '')) .
				$this->htmlHidden("selTempl", (isset($this->export->selTempl) ? $this->export->selTempl : '')) .
				$this->htmlHidden("selObjs", (isset($this->export->selObjs) ? $this->export->selObjs : '')) .
				$this->htmlHidden("selClasses", (isset($this->export->selClasses) ? $this->export->selClasses : '')) .
				$this->htmlHidden("selDocs_open", we_base_request::_(we_base_request::INTLIST, "selDocs_open", '')) .
				$this->htmlHidden("selTempl_open", we_base_request::_(we_base_request::INTLIST, "selTempl_open", '')) .
				$this->htmlHidden("selObjs_open", we_base_request::_(we_base_request::INTLIST, "selObjs_open", '')) .
				$this->htmlHidden("selClasses_open", we_base_request::_(we_base_request::INTLIST, "selClasses_open", ''));
	}

	function getJSTop(){
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';

		return
				parent::getJSTop() .
				we_html_element::jsElement('
var get_focus = 1;
var activ_tab = 1;
var hot= 0;
var scrollToVal=0;
var table = "' . FILE_TABLE . '";

function setHot() {
	hot = "1";
}

function usetHot() {
	hot = "0";
}

function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

parent.document.title = "' . $title . '";

function we_cmd() {
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	if(hot == "1" && arguments[0] != "save_export") {
		if(confirm("' . g_l('export', '[save_changed_export]') . '")) {
			arguments[0] = "save_export";
		} else {
			top.content.usetHot();
		}
	}
	switch (arguments[0]) {
		case "exit_export":
			if(hot != "1") {
				eval(\'top.opener.top.we_cmd("exit_modules")\');
			}
					break;
		case "new_export_group":
			' . (!permissionhandler::hasPerm("NEW_EXPORT") ? we_message_reporting::getShowMessageCall(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR) . 'return;' : '') . '
			if(' . $this->editorBodyFrame . '.loaded) {
				' . $this->editorBodyForm . '.IsFolder.value = 1;
			}
		case "new_export":
			' . (!permissionhandler::hasPerm("NEW_EXPORT") ? we_message_reporting::getShowMessageCall(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR) . 'return;' : '') . '
			if(' . $this->editorBodyFrame . '.loaded) {
				' . $this->editorBodyForm . '.cmd.value = arguments[0];
				' . $this->editorBodyForm . '.cmdid.value = arguments[1];
				' . $this->editorBodyForm . '.pnt.value = "edbody";
				' . $this->editorBodyForm . '.tabnr.value = 1;
				' . $this->editorBodyFrame . '.submitForm();
			} else {
				setTimeout("we_cmd("+arguments[0]+");", 10);
			}
		break;
		case "delete_export":
			if(' . $this->editorBodyForm . '.cmd.value=="home") return;
			' . (!permissionhandler::hasPerm("DELETE_EXPORT") ?
								(
								we_message_reporting::getShowMessageCall(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
								) :
								('
					if (' . $this->editorBodyFrame . '.loaded) {
						var message="' . g_l('export', '[delete_question]') . '";
						if(' . $this->editorBodyForm . '.IsFolder.value=="1") message = "' . g_l('export', '[delete_group_question]') . '";

						if (confirm(message)) {
							' . $this->editorBodyForm . '.cmd.value=arguments[0];
							' . $this->editorBodyForm . '.pnt.value = "cmd" ;
							' . $this->editorBodyForm . '.tabnr.value=' . $this->topFrame . '.activ_tab;
							' . $this->editorBodyFrame . '.submitForm("cmd");
						}
					} else {
						' . we_message_reporting::getShowMessageCall(g_l('export', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
					}

			')) . '
		break;
		case "start_export":
					if(' . $this->topFrame . '.hot!=0){
						' . we_message_reporting::getShowMessageCall(g_l('export', '[must_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						break;
					}
					' . (!permissionhandler::hasPerm("NEW_EXPORT") ? we_message_reporting::getShowMessageCall(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR) . 'return;' : ''
						) . '
					if (' . $this->topFrame . '.editor.edheader.setTab) ' . $this->topFrame . '.editor.edheader.setActiveTab("tab_3");
					if (' . $this->topFrame . '.editor.edheader.setTab) ' . $this->topFrame . '.editor.edheader.setTab(3);
					if (' . $this->topFrame . '.editor.edfooter.doProgress) ' . $this->topFrame . '.editor.edfooter.doProgress(0);
					if (' . $this->editorBodyFrame . '.clearLog) ' . $this->editorBodyFrame . '.clearLog();
					if (' . $this->editorBodyFrame . '.addLog) ' . $this->editorBodyFrame . '.addLog("' . addslashes(we_html_tools::getPixel(10, 10)) . '<br/>");
		case "save_export":
			' . (!permissionhandler::hasPerm("NEW_EXPORT") ? we_message_reporting::getShowMessageCall(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR) . 'return;' : ''
						) . '
			if(' . $this->editorBodyForm . '.cmd.value=="home") return;

			if (' . $this->editorBodyFrame . '.loaded) {
							if(' . $this->editorBodyForm . '.Text.value==""){
								' . we_message_reporting::getShowMessageCall(g_l('export', '[name_empty]'), we_message_reporting::WE_MESSAGE_ERROR) . '
								return;
							}
							' . $this->editorBodyForm . '.cmd.value=arguments[0];
							' . $this->editorBodyForm . '.pnt.value=arguments[0]=="start_export" ? "load" : "edbody";
							' . $this->editorBodyForm . '.tabnr.value=' . $this->topFrame . '.activ_tab;
							if(' . $this->editorBodyForm . '.IsFolder.value!=1){
								' . $this->editorBodyForm . '.selDocs.value=' . $this->editorBodyFrame . '.SelectedItems["' . FILE_TABLE . '"].join(",");
								' . $this->editorBodyForm . '.selTempl.value=' . $this->editorBodyFrame . '.SelectedItems["' . TEMPLATES_TABLE . '"].join(",");
								' . (defined('OBJECT_FILES_TABLE') ? $this->editorBodyForm . '.selObjs.value=' . $this->editorBodyFrame . '.SelectedItems["' . OBJECT_FILES_TABLE . '"].join(",");' : '') . '
								' . (defined('OBJECT_TABLE') ? $this->editorBodyForm . '.selClasses.value=' . $this->editorBodyFrame . '.SelectedItems["' . OBJECT_TABLE . '"].join(",");' : '') . '

								' . $this->editorBodyForm . '.selDocs_open.value=' . $this->editorBodyFrame . '.openFolders["' . FILE_TABLE . '"];
								' . $this->editorBodyForm . '.selTempl_open.value=' . $this->editorBodyFrame . '.openFolders["' . TEMPLATES_TABLE . '"];
								' . (defined('OBJECT_FILES_TABLE') ? $this->editorBodyForm . '.selObjs_open.value=' . $this->editorBodyFrame . '.openFolders["' . OBJECT_FILES_TABLE . '"];' : '') . '
								' . (defined('OBJECT_TABLE') ? $this->editorBodyForm . '.selClasses_open.value=' . $this->editorBodyFrame . '.openFolders["' . OBJECT_TABLE . '"];' : '') . '
							}

							' . $this->editorBodyFrame . '.submitForm(arguments[0]=="start_export" ? "cmd" : "edbody");
			} else {
				' . we_message_reporting::getShowMessageCall(g_l('export', '[nothing_to_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			}
			top.content.usetHot();
		break;

		case "export_edit":
			' . (!permissionhandler::hasPerm("EDIT_EXPORT") ? we_message_reporting::getShowMessageCall(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR) . 'return;' : ''
						) . '
			' . $this->topFrame . '.hot=0;
			' . $this->editorBodyForm . '.cmd.value=arguments[0];
			' . $this->editorBodyForm . '.pnt.value="edbody";
			' . $this->editorBodyForm . '.cmdid.value=arguments[1];
			' . $this->editorBodyForm . '.tabnr.value=' . $this->topFrame . '.activ_tab;

			' . $this->editorBodyFrame . '.submitForm();
		break;
		case "load":
			' . $this->topFrame . '.cmd.location="' . $this->frameset . '?pnt=cmd&pid="+arguments[1]+"&offset="+arguments[2]+"&sort="+arguments[3];
		break;
		case "home":
			' . $this->editorBodyFrame . '.parent.location="' . $this->frameset . '?pnt=editor";
		break;
		default:
			for (var i = 0; i < arguments.length; i++) {
				args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
			}
			eval("top.opener.top.we_cmd(" + args + ")");
	}
}');
	}

	function getJSProperty(){
		$selected = '';
		$opened = '';
		$arr = array(FILE_TABLE => "selDocs", TEMPLATES_TABLE => "selTempl");
		if(defined('OBJECT_TABLE')){
			$arr[OBJECT_FILES_TABLE] = "selObjs";
			$arr[OBJECT_TABLE] = "selClasses";
		}

		foreach($arr as $table => $elem){
			$items = makeArrayFromCSV($this->export->$elem);
			foreach($items as $item){
				$selected .= 'SelectedItems["' . $table . '"].push("' . $item . '");';
			}

			if(($open = we_base_request::_(we_base_request::STRING, $elem . '_open'))){
				$opened .= 'openFolders["' . $table . '"]="' . $open . '";';
			}
		}

		return parent::getJSProperty() .
				we_html_element::jsElement('
var loaded=0;
var table = "' . we_base_request::_(we_base_request::TABLE, "table", FILE_TABLE) . '";

function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function we_cmd() {
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

	switch (arguments[0]) {
		case "switchPage":
			document.we_form.cmd.value=arguments[0];
			document.we_form.tabnr.value=arguments[1];
			submitForm();
			break;
		case "export_openDirselector":
			url="' . WE_MODULES_DIR . 'export/we_exportDirSelectorFrameset.php?";
			for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
			new jsWindow(url,"we_exportselector",-1,-1,600,350,true,true,true);
			break;
		case "openCatselector":
			new jsWindow(url,"we_catselector",-1,-1,' . we_selector_file::WINDOW_CATSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_CATSELECTOR_HEIGHT . ',true,true,true,true);
		break;
		case "openDirselector":
			new jsWindow(url,"we_selector",-1,-1,' . we_selector_file::WINDOW_SELECTOR_WIDTH . ',' . we_selector_file::WINDOW_SELECTOR_HEIGHT . ',true,true,true,true);
		break;
		case "add_cat":
		case "del_cat":
		case "del_all_cats":
			document.we_form.cmd.value=arguments[0];
			' . $this->editorBodyForm . '.pnt.value="edbody";
			document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
			document.we_form.cat.value=arguments[1];
			submitForm();
		break;
		default:
			for (var i = 0; i < arguments.length; i++) {
				args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
			}
			eval("' . $this->topFrame . '.we_cmd("+args+")");
	}
}' .
						$this->getJSSubmitFunction() . '
function start() {
	' . $selected . $opened . ( $this->export->IsFolder == 0 ? '
	setHead(' . $this->editorBodyFrame . '.table);' : '') . '
}');
	}

	function getJSTreeHeader(){
		return '
function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function we_cmd(){
	var args = "";
	var url = "' . $this->frameset . '?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]) {
		default:
			for (var i = 0; i < arguments.length; i++) {
				args += \'arguments[\'+i+\']\' + ((i < (arguments.length-1)) ? \',\' : \'\');
			}
			eval(\'' . $this->topFrame . '.we_cmd(\'+args+\')\');
	}
}' . $this->getJSSubmitFunction("cmd");
	}

	function getJSSubmitFunction($def_target = "edbody", $def_method = "post"){
		return '
			function submitForm() {
				var f = self.document.we_form;

				if (arguments[0]) {
					f.target = arguments[0];
				} else {
					f.target = "' . $def_target . '";
				}

				if (arguments[1]) {
					f.action = arguments[1];
				} else {
					f.action = "' . $this->frameset . '";
				}

				if (arguments[2]) {
					f.method = arguments[2];
				} else {
					f.method = "' . $def_method . '";
				}

				f.submit();
			}

	';
	}

	function processCommands(){
		switch(we_base_request::_(we_base_request::STRING, "cmd")){
			case "new_export":
				if(!permissionhandler::hasPerm("NEW_EXPORT")){
					print we_html_element::jsElement(
									we_message_reporting::getShowMessageCall(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				} else {
					$this->export = new we_export_export();
					print we_html_element::jsElement('
								' . $this->topFrame . '.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->export->Text) . '";
								' . $this->topFrame . '.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";
						');
				}

				break;
			case "new_export_group":
				if(!permissionhandler::hasPerm("NEW_EXPORT")){
					print we_html_element::jsElement(
									we_message_reporting::getShowMessageCall(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				} else {
					$this->export = new we_export_export();
					$this->export->Text = g_l('export', '[newFolder]');
					$this->export->IsFolder = 1;
					echo we_html_element::jsElement('
								' . $this->topFrame . '.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->export->Text) . '";
								' . $this->topFrame . '.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";
						');
				}
				break;
			case "export_edit":
				if(!permissionhandler::hasPerm("EDIT_EXPORT")){
					echo we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				}
				$this->export = new we_export_export(we_base_request::_(we_base_request::INT, "cmdid"));
				echo we_html_element::jsElement('
								' . $this->topFrame . '.hot=0;
								' . $this->topFrame . '.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->export->Text) . '";
								' . $this->topFrame . '.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";
						');

				break;
			case "save_export":
				if(!permissionhandler::hasPerm("NEW_EXPORT")){
					echo we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				}
				$js = "";
				if($this->export->filenameNotValid($this->export->Text)){
					echo we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('export', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				}
				// check if filename is valid.
				if($this->export->exportToFilenameValid($this->export->Filename)){
					echo we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('export', '[wrongfilename]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				}

				if(!trim($this->export->Text)){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('export', '[name_empty]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
				$oldpath = $this->export->Path;
				// set the path and check it
				$this->export->setPath();
				if($this->export->pathExists($this->export->Path)){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('export', '[name_exists]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
				if($this->export->isSelf()){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('export', '[path_nok]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if($this->export->ParentID > 0){
					$weAcQuery = new we_selector_query();
					$weAcResult = $weAcQuery->getItemById($this->export->ParentID, EXPORT_TABLE, array("IsFolder"));
					if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
						echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('export', '[path_nok]'), we_message_reporting::WE_MESSAGE_ERROR));
						break;
					}
				}
				if(isset($this->export->Folder) && !empty($this->export->Folder) && $this->export->ParentID > 0){
					$weAcQuery = new we_selector_query();
					$weAcResult = $weAcQuery->getItemById($this->export->Folder, FILE_TABLE, array("IsFolder"));
					if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
						echo we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('export', '[path_nok]'), we_message_reporting::WE_MESSAGE_ERROR)
						);
						break;
					}
				}

				$newone = true;
				if($this->export->ID){
					$newone = false;
				}

				$this->export->save();

				if($this->export->IsFolder && $oldpath != '' && $oldpath != '/' && $oldpath != $this->export->Path){
					$db_tmp = new DB_WE();
					$this->db->query('SELECT ID FROM ' . EXPORT_TABLE . ' WHERE Path LIKE \'' . $this->db->escape($oldpath) . '%\' AND ID!=' . intval($this->export->ID) . ';');
					while($this->db->next_record()){
						$db_tmp->query('UPDATE ' . EXPORT_TABLE . ' SET Path=\'' . $this->export->evalPath($this->db->f("ID")) . '\' WHERE ID=' . $this->db->f("ID") . ';');
					}
				}

				$js = ($newone ?
								$this->topFrame . '.makeNewEntry(\'' . $this->export->Icon . '\',\'' . $this->export->ID . '\',\'' . $this->export->ParentID . '\',\'' . $this->export->Text . '\',0,\'' . ($this->export->IsFolder ? 'folder' : 'item') . '\',\'' . EXPORT_TABLE . '\');' .
								$this->topFrame . '.drawTree();' :
								$this->topFrame . '.updateEntry(' . $this->export->ID . ',"' . $this->export->Text . '","' . $this->export->ParentID . '");'
						);
				echo we_html_element::jsElement(
						$js .
						$this->editorHeaderFrame . '.location.reload();' .
						we_message_reporting::getShowMessageCall(g_l('export', ($this->export->IsFolder == 1 ? '[save_group_ok]' : '[save_ok]')), we_message_reporting::WE_MESSAGE_NOTICE) .
						$this->topFrame . '.hot=0;'
				);

				break;
			case "delete_export":
				if(!permissionhandler::hasPerm("DELETE_EXPORT")){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
					return;
				}

				if($this->export->delete()){
					echo we_html_element::jsElement('
									' . $this->topFrame . '.deleteEntry(' . $this->export->ID . ');
									' . we_message_reporting::getShowMessageCall(g_l('export', ($this->export->IsFolder ? '[delete_group_ok]' : '[delete_ok]')), we_message_reporting::WE_MESSAGE_NOTICE) . '
									' . $this->topFrame . '.we_cmd("home");
							');
					$this->export = new we_export_export();
				} else {
					echo we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('export', ($this->export->IsFolder == 1 ? '[delete_group_nok]' : '[delete_nok]')), we_message_reporting::WE_MESSAGE_ERROR)
					);
				}

				break;
			case "start_export":
				we_exim_XMLExIm::unsetPerserves();
				$_REQUEST["cmd"] = "do_export";
				$this->export->ExportFilename = ($this->export->ExportTo === 'local' ? TEMP_PATH . $this->export->Filename : $_SERVER['DOCUMENT_ROOT'] . $this->export->ServerPath . "/" . $this->export->Filename);
				break;
			default:
		}

		$_SESSION['weS']['export_session'] = $this->export;
	}

	function processVariables(){//FIXME use table datatypes
		if(isset($_SESSION['weS']['export_session'])){
			$this->export = $_SESSION['weS']['export_session'];
		}

		if(isset($_SESSION['weS']['exportVars_session'])){
			unset($_SESSION['weS']['exportVars_session']);
		}

		if(is_array($this->export->persistent_slots)){
			foreach($this->export->persistent_slots as $varname){
				if(($v = we_base_request::_(we_base_request::STRINGC, $varname))){//FIXME: this is quiet for now....
					$this->export->{$varname} = $v;
				}
			}
		}
		$this->page = we_base_request::_(we_base_request::INT, 'page', $this->page);
	}

}
