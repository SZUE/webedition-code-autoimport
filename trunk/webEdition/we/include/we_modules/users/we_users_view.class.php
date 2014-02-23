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
/* the parent class of storagable webEdition classes */


class we_users_view extends weModuleView{

	var $db;
	var $frameset;
	var $topFrame;
	var $raw;

	function __construct($frameset = '', $topframe = 'top.content'){
		$this->db = new DB_WE();
		$this->setFramesetName($frameset);
		$this->setTopFrame($topframe);
		//$this->raw = new weShop();
	}

	//----------- Utility functions ------------------

	function htmlHidden($name, $value = ''){
		return we_html_element::htmlHidden(array('name' => trim($name), 'value' => oldHtmlspecialchars($value)));
	}

	//-----------------Init -------------------------------

	function setFramesetName($frameset){
		$this->frameset = $frameset;
	}

	function setTopFrame($frame){
		$this->topFrame = $frame;
	}

	//------------------------------------------------


	function getCommonHiddens($cmds = array()){
		return $this->htmlHidden('cmd', (isset($cmds['cmd']) ? $cmds['cmd'] : '')) .
			$this->htmlHidden('cmdid', (isset($cmds['cmdid']) ? $cmds['cmdid'] : '')) .
			$this->htmlHidden('pnt', (isset($cmds['pnt']) ? $cmds['pnt'] : '')) .
			$this->htmlHidden('tabnr', (isset($cmds['tabnr']) ? $cmds['tabnr'] : ''));
	}

	function getJSTop_tmp(){
		$mod = isset($_REQUEST['mod']) ? $_REQUEST['mod'] : '';
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';

		$jsCode = '
		var loaded=0;
		var hot=0;
		var hloaded=0;

		parent.document.title = "' . $title . '";

		';

		$jsCode .= '
		var cgroup=' . ($_SESSION['user']['ID'] ? intval(f('SELECT ParentID FROM ' . USER_TABLE . ' WHERE ID=' . $_SESSION["user"]["ID"], 'ParentID', $GLOBALS['DB_WE'])) : 0) . ';';
		if(isset($_SESSION['user_session_data'])){
			unset($_SESSION['user_session_data']);
		}

		$jsCode .= '
		function doUnload(){
			if(!!jsWindow_count){
				for(i = 0; i < jsWindow_count; i++){
					eval("jsWindow" + i + "Object.close()");
				}
			}
		}

		function we_cmd() {
			var args = "";
			var url = "' . WEBEDITION_DIR . 'we_cmd.php?";

			for(var i = 0; i < arguments.length; i++) {
				url += "we_cmd["+i+"]="+escape(arguments[i]);
				if(i < (arguments.length - 1)) {
					url += "&";
				}
			}

			if(hot == "1" && arguments[0] != "save_user") {
				if(confirm("' . g_l('modules_users', '[save_changed_user]') . '")) {
					arguments[0] = "save_user";
				} else {
					top.content.usetHot();
				}
			}
			switch (arguments[0]) {
				case "exit_users":
					if(hot != "1") {
						eval("top.opener.top.we_cmd(\'exit_modules\')");//imi: test
					}
					break;
				case "new_user":
					top.content.editor.edbody.focus();
					if(hot==1 && top.content.editor.edbody.document.we_form.ucmd) {
						if(confirm("' . g_l('modules_users', "[save_changed_user]") . '")) {
							top.content.editor.edbody.document.we_form.ucmd.value="save_user";
							top.content.editor.edbody.document.we_form.sd.value=1;
						} else {
							top.content.usetHot();
							top.content.editor.edbody.document.we_form.ucmd.value="new_user";
						}
						if(arguments[1]){
							top.content.editor.edbody.document.we_form.uid.value=arguments[1];
						}
						if(arguments[2]){
							top.content.editor.edbody.document.we_form.ctype.value=arguments[2];
						}
						if(arguments[3]){
							top.content.editor.edbody.document.we_form.ctable.value=arguments[3];
						}
						top.content.editor.edbody.we_submitForm("cmd","' . $this->frameset . '?pnt=cmd");
					} else {
						top.content.cmd.location="' . $this->frameset . '?pnt=cmd&ucmd=new_user&cgroup="+cgroup;
					}
					break;
				case "check_user_display":
					top.content.cmd.location="' . $this->frameset . '?pnt=cmd&ucmd=check_user_display&uid="+arguments[1];
					break;
				case "display_user":
					top.content.editor.edbody.focus();
					if(hot==1 && top.content.editor.edbody.document.we_form.ucmd) {
						if(confirm("' . g_l('modules_users', '[save_changed_user]') . '")) {
							top.content.editor.edbody.document.we_form.ucmd.value="save_user";
							top.content.editor.edbody.document.we_form.sd.value=1;
						}
						else {
							top.content.usetHot();
							top.content.editor.edbody.document.we_form.ucmd.value="display_user";
						}
						if(arguments[1])
							top.content.editor.edbody.document.we_form.uid.value=arguments[1];
						if(arguments[2])
							top.content.editor.edbody.document.we_form.ctype.value=arguments[2];
						if(arguments[3])
							top.content.editor.edbody.document.we_form.ctable.value=arguments[3];
						top.content.editor.edbody.we_submitForm("cmd","' . $this->frameset . '?pnt=cmd");
					}
					else {
						top.content.cmd.location="' . $this->frameset . '?pnt=cmd&ucmd=display_user&uid="+arguments[1];
					}
					break;
				case "display_alias":
					top.content.editor.edbody.focus();
					top.content.editor.edbody.document.we_form.ucmd.value="display_user";
					if(hot==1 && top.content.editor.edbody.document.we_form.ucmd) {
						if(confirm("' . g_l('modules_users', '[save_changed_user]') . '")) {
							top.content.editor.edbody.document.we_form.ucmd.value="save_user";
							top.content.editor.edbody.document.we_form.sd.value=1;
						}
						else {
							top.content.usetHot();
							top.content.editor.edbody.document.we_form.ucmd.value="display_user";
						}
						if(arguments[1]){
							top.content.editor.edbody.document.we_form.uid.value=arguments[1];
						}
						if(arguments[2]){
							top.content.editor.edbody.document.we_form.ctype.value=arguments[2];
						}
						if(arguments[3]){
							top.content.editor.edbody.document.we_form.ctable.value=arguments[3];
						}
						top.content.editor.edbody.we_submitForm("cmd","' . $this->frameset . '?pnt=cmd");
					}
					else {
						top.content.cmd.location="' . $this->frameset . '?pnt=cmd&ucmd=display_user&uid="+arguments[1];
					}
					break;
				case "new_group":
					if(hot==1 && top.content.editor.edbody.document.we_form.ucmd) {
						if(confirm("' . g_l('modules_users', '[save_changed_user]') . '")) {
							top.content.editor.edbody.document.we_form.ucmd.value="save_user";
							top.content.editor.edbody.document.we_form.sd.value=1;
						} else {
							top.content.usetHot();
							top.content.editor.edbody.document.we_form.ucmd.value="new_group";
						}
						if(arguments[1])
							top.content.editor.edbody.document.we_form.uid.value=arguments[1];
						if(arguments[2])
							top.content.editor.edbody.document.we_form.ctype.value=arguments[2];
						if(arguments[3])
							top.content.editor.edbody.document.we_form.ctable.value=arguments[3];
						top.content.editor.edbody.we_submitForm("cmd","' . $this->frameset . '?pnt=cmd");
					} else {
						top.content.cmd.location="' . $this->frameset . '?pnt=cmd&ucmd=new_group&cgroup="+cgroup;
					}
					break;
				case "new_alias":
					if(hot==1 && top.content.editor.edbody.document.we_form.ucmd) {
						if(confirm("' . g_l('modules_users', "[save_changed_user]") . '")) {
							top.content.editor.edbody.document.we_form.ucmd.value="save_user";
							top.content.editor.edbody.document.we_form.sd.value=1;
						} else {
							top.content.usetHot();
							top.content.editor.edbody.document.we_form.ucmd.value="new_alias";
						}
						if(arguments[1])
							top.content.editor.edbody.document.we_form.uid.value=arguments[1];
						if(arguments[2])
							top.content.editor.edbody.document.we_form.ctype.value=arguments[2];
						if(arguments[3])
							top.content.editor.edbody.document.we_form.ctable.value=arguments[3];
						top.content.editor.edbody.we_submitForm("cmd","' . $this->frameset . '?pnt=cmd");
					} else {
						top.content.cmd.location="' . $this->frameset . '?pnt=cmd&ucmd=new_alias&cgroup="+cgroup;
					}
					break;
				case "save_user":
					if(top.content.editor.edbody.document.we_form) {
						top.content.editor.edbody.document.we_form.ucmd.value="save_user";
						top.content.usetHot();
						top.content.editor.edbody.we_submitForm("cmd","' . $this->frameset . '?pnt=cmd");
					}
					break;
				case "delete_user":
					top.content.cmd.location="' . $this->frameset . '?pnt=cmd&ucmd=delete_user";
					break;
				case "search":
					new jsWindow("' . WE_USERS_MODULE_DIR . 'edit_users_sresults.php?kwd="+arguments[1],"customer_settings",-1,-1,580,400,true,false,true);
					break;
				case "new_organization":
					var orgname = prompt("' . g_l('modules_users', "[give_org_name]") . '","");
					if(orgname!= null) {
						top.content.cmd.location="' . $this->frameset . '?pnt=cmd&ucmd=new_organization&orn="+orgname;
					}
					break;
				default:
					for(var i = 0; i < arguments.length; i++) {
						args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
					}
					eval("opener.top.content.we_cmd("+args+")");
			}
		}

		function setHot() {
			hot=1;
		}

		function usetHot() {
			hot=0;
		}
		';

		return we_html_element::jsScript(JS_DIR . 'images.js') .
			we_html_element::jsScript(JS_DIR . 'windows.js') .
			we_html_element::jsScript(JS_DIR . 'md5.js') .
			we_html_element::jsElement($jsCode);
	}

	function getJSTop(){//TODO: is this shop-code or a copy paste from another module?
		return we_html_element::jsScript(JS_DIR . 'windows.js') . we_html_element::jsElement('
var get_focus = 1;
var activ_tab = 1;
var hot= 0;
var scrollToVal=0;

function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function we_cmd() {
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]) {
		case "new_raw":
			if(' . $this->topFrame . '.editor.edbody.loaded) {
				' . $this->topFrame . '.hot = 1;
				' . $this->topFrame . '.editor.edbody.document.we_form.cmd.value = arguments[0];
				' . $this->topFrame . '.editor.edbody.document.we_form.cmdid.value = arguments[1];
				' . $this->topFrame . '.editor.edbody.document.we_form.tabnr.value = 1;
				' . $this->topFrame . '.editor.edbody.submitForm();
			} else {
				setTimeout(\'we_cmd("new_raw");\', 10);
			}
			break;

		case "delete_raw":
			if(top.content.editor.edbody.document.we_form.cmd.value=="home") return;
			' . (!permissionhandler::hasPerm("DELETE_RAW") ?
					( we_message_reporting::getShowMessageCall(g_l('modules_shop', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)) :
					('
					if (' . $this->topFrame . '.editor.edbody.loaded) {
						if (confirm("' . g_l('modules_shop', '[delete_alert]') . '")) {
							' . $this->topFrame . '.editor.edbody.document.we_form.cmd.value=arguments[0];
							' . $this->topFrame . '.editor.edbody.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
							' . $this->topFrame . '.editor.edbody.submitForm();
						}
					} else {
						' . we_message_reporting::getShowMessageCall(g_l('modules_shop', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
					}

			')) . '
			break;

		case "save_raw":
			if(top.content.editor.edbody.document.we_form.cmd.value=="home") return;


					if (' . $this->topFrame . '.editor.edbody.loaded) {
							' . $this->topFrame . '.editor.edbody.document.we_form.cmd.value=arguments[0];
							' . $this->topFrame . '.editor.edbody.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;

							' . $this->topFrame . '.editor.edbody.submitForm();
					} else {
						' . we_message_reporting::getShowMessageCall(g_l('modules_shop', '[nothing_to_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
					}

			break;

		case "edit_raw":
			' . $this->topFrame . '.hot=0;
			' . $this->topFrame . '.editor.edbody.document.we_form.cmd.value=arguments[0];
			' . $this->topFrame . '.editor.edbody.document.we_form.cmdid.value=arguments[1];
			' . $this->topFrame . '.editor.edbody.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
			' . $this->topFrame . '.editor.edbody.submitForm();
		break;
		case "load":
			' . $this->topFrame . '.cmd.location="' . $this->frameset . '?pnt=cmd&pid="+arguments[1]+"&offset="+arguments[2]+"&sort="+arguments[3];
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
		return weSuggest::getYuiFiles() .
			we_html_element::jsScript(JS_DIR . 'images.js') .
			we_html_element::jsScript(JS_DIR . 'windows.js') .
			we_html_element::jsScript(JS_DIR . 'md5.js') .
			we_html_element::jsElement('
var loaded = 0;
function we_submitForm(target, url) {
	var f = self.document.we_form;

	ok = true;

	if (f.input_pass) {
		if (f.oldtab.value == 0) {
			if (f.input_pass.value.length < 4 && f.input_pass.value.length != 0) {
				' . we_message_reporting::getShowMessageCall(g_l('modules_users', '[password_alert]'), we_message_reporting::WE_MESSAGE_ERROR) . '
				return false;
			} else {
				if (f.input_pass.value != "") {
					var clearPass = f.input_pass.value;
					f.input_pass.value = "";
					eval("f." + f.obj_name.value + "_clearpasswd.value = clearPass;");
				}
			}
		}
	}

	if (ok) {
		f.target = target;
		f.action = url;
		f.method = "post";
		f.submit();
	}
	return true;
}

function switchPage(page) {
	document.we_form.tab.value = page;
	return we_submitForm(self.name, "' . $this->frameset . '?pnt=edbody");
}


function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function we_cmd() {
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + escape(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}

	switch (arguments[0]) {
		case "browse_users":
			new jsWindow(url, "browse_users", -1, -1, 500, 300, true, false, true);
			break;

		case "openDirselector":
			new jsWindow(url, "we_fileselector", -1, -1,' . we_selector_file::WINDOW_DIRSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DIRSELECTOR_HEIGHT . ', true, true, true, true);
			break;

		case "select_seem_start":
			myWind = false;

			for (k = top.opener.top.jsWindow_count; k > -1; k--) {

				eval("if(top.opener.top.jsWindow" + k + "Object){" +
								"	if(top.opener.top.jsWindow" + k + "Object.ref == \'edit_module\'){" +
								"		myWind = top.opener.top.jsWindow" + k + "Object.wind.content.editor.edbody;" +
								"		myWindStr = \'top.jsWindow" + k + "Object.wind.content.editor.edbody\';" +
								"	}" +
								"}");
				if (myWind) {
					break;
				}
			}

			top.opener.top.we_cmd("openDocselector", myWind.document.forms[0].elements["seem_start_file"].value, "' . FILE_TABLE . '", myWindStr + ".document.forms[0].elements[\'seem_start_file\'].value", myWindStr + ".document.forms[0].elements[\'seem_start_file_name\'].value", "", "' . session_id() . '", "", "text/webedition", 1);

			break;
		case "openNavigationDirselector":
		case "openNewsletterDirselector":
			if (arguments[0] == "openNewsletterDirselector") {
				url = "' . WE_MODULES_DIR . 'newsletter/we_dirfs.php?";
			}
			else {
				url = "' . WE_INCLUDES_DIR . 'we_modules/navigation/we_navigationDirSelect.php?";
			}
			for (var i = 0; i < arguments.length; i++) {
				url += "we_cmd[" + i + "]=" + escape(arguments[i]);
				if (i < (arguments.length - 1)) {
					url += "&";
				}
			}
			new jsWindow(url, "we_navigation_dirselector", -1, -1, 600, 400, true, true, true);
			break;
		default:
			for (var i = 0; i < arguments.length; i++) {
				args += "arguments[" + i + "]" + ((i < (arguments.length - 1)) ? "," : "");
			}
			eval("top.content.we_cmd(" + args + ")");
			break;
	}
}
		');
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
	var url = "' . $this->frameset . '?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]) {
		default:
			for (var i = 0; i < arguments.length; i++) {
				args += \'arguments[\'+i+\']\' + ((i < (arguments.length-1)) ? \',\' : \'\');
			}
			eval(\'top.content.we_cmd(\'+args+\')\');
	}
}' .
			$this->getJSSubmitFunction("cmd");
	}

	function processCommands(){
		if(isset($_REQUEST["ucmd"])){
			switch($_REQUEST["ucmd"]){
				case "new_group":
					if(!permissionhandler::hasPerm("NEW_GROUP")){
						print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
						break;
					}

					$user_object = new we_users_user();

					if(isset($_REQUEST["cgroup"]) && $_REQUEST["cgroup"]){
						$user_group = new we_users_user();
						if($user_group->initFromDB($_REQUEST["cgroup"])){
							$user_object->ParentID = $_REQUEST["cgroup"];
						}
					}

					$user_object->initType(we_users_user::TYPE_USER_GROUP);

					$_SESSION["user_session_data"] = $user_object->getState();

					print we_html_element::jsElement('
		top.content.editor.edheader.location="' . $this->frameset . '?pnt=edheader";
		top.content.editor.edbody.location="' . $this->frameset . '?pnt=edbody";
		top.content.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";');
					break;

				case "new_alias":
					if(!permissionhandler::hasPerm("NEW_USER")){
						print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
						break;
					}

					$user_object = new we_users_user();

					if(isset($_REQUEST["cgroup"]) && $_REQUEST["cgroup"]){
						$user_group = new we_users_user();
						if($user_group->initFromDB($_REQUEST["cgroup"])){
							$user_object->ParentID = $_REQUEST["cgroup"];
						}
					}

					$user_object->initType(we_users_user::TYPE_ALIAS);

					$_SESSION["user_session_data"] = $user_object->getState();
					print we_html_element::jsElement('
		top.content.editor.edheader.location="' . $this->frameset . '?pnt=edheader";
		top.content.editor.edbody.location="' . $this->frameset . '?pnt=edbody";
		top.content.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";');
					break;

				case "search":
					print we_html_element::jsElement('
				    top.content.editor.edbody.location="' . WE_USERS_MODULE_DIR . 'edit_users_sresults.php?kwd=' . $_REQUEST["kwd"] . '";
				');
					break;

				case "display_alias":
					if($uid && $ctype && $ctable){
						print we_html_element::jsElement('
		top.content.usetHot();
		top.content.editor.edheader.location="' . $this->frameset . '?pnt=edheader&uid=".$uid."&ctype=".ctype."&ctable=".$ctable;
		top.content.editor.edbody.location="' . $this->frameset . '?pnt=edbody&uid=".$uid."&ctype=".ctype."&ctable=".$ctable;
		top.content.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter&uid=".$uid."&ctype=".ctype."&ctable=".$ctable;');
					}
					break;

				case "new_user":
					if(!permissionhandler::hasPerm("NEW_USER")){
						print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
						break;
					}
					$user_object = new we_users_user();

					if(isset($_REQUEST["cgroup"]) && $_REQUEST["cgroup"]){
						$user_group = new we_users_user();
						if($user_group->initFromDB($_REQUEST["cgroup"])){
							$user_object->ParentID = $_REQUEST["cgroup"];
						}
					}
					$user_object->initType(we_users_user::TYPE_USER);

					$_SESSION["user_session_data"] = $user_object->getState();
					print we_html_element::jsElement('
		top.content.editor.edheader.location="' . $this->frameset . '?pnt=edheader";
		top.content.editor.edbody.location="' . $this->frameset . '?pnt=edbody&oldtab=0";
		top.content.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";');
					break;
				case "display_user":
					if($_REQUEST["uid"]){
						$user_object = new we_users_user();
						$user_object->initFromDB($_REQUEST['uid']);
						if(!permissionhandler::hasPerm("ADMINISTRATOR") && $user_object->checkPermission("ADMINISTRATOR")){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
							$user_object = new we_users_user();
							break;
						}

						$_SESSION["user_session_data"] = $user_object->getState();
						$setgroup = "";
						if($user_object->Type == 1){
							$setgroup = 'top.content.cgroup=' . $user_object->ID . ";\n";
						}
						print we_html_element::jsElement('
		top.content.usetHot();
		' . $setgroup . '
		top.content.editor.edheader.location="' . $this->frameset . '?pnt=edheader";
		top.content.editor.edbody.location="' . $this->frameset . '?pnt=edbody&oldtab=0";
		top.content.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";');
					}
					break;
				case "save_user":
					$isAcError = false;
					$weAcQuery = new we_selector_query();

					// bugfix #1665 for php 4.1.2: "-" moved to the end of the regex-pattern
					if(isset($_REQUEST[$_REQUEST['obj_name'] . '_username']) && !we_users_user::filenameNotValid($_REQUEST[$_REQUEST['obj_name'] . '_username'])){
						print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('global', '[username_wrong_chars]'), we_message_reporting::WE_MESSAGE_ERROR));
						break;
					}
					if(!isset($_SESSION['user_session_data'])){
						print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
						break;
					}
					if(isset($_REQUEST[$_REQUEST['obj_name'] . '_ParentID']) && !empty($_REQUEST[$_REQUEST['obj_name'] . '_ParentID']) && $_REQUEST[$_REQUEST['obj_name'] . '_ParentID'] > 0){
						$weAcResult = $weAcQuery->getItemById($_REQUEST[$_REQUEST['obj_name'] . '_ParentID'], USER_TABLE, array('IsFolder'), false);
						if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}
					}
					$i = 0;
					while(isset($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . FILE_TABLE . '_' . $i]) && !empty($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . FILE_TABLE . '_' . $i])){
						$weAcResult = $weAcQuery->getItemById($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . FILE_TABLE . '_' . $i], FILE_TABLE, array("IsFolder"));
						if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
							$isAcError = true;
							break;
						}
						$i++;
					}
					$i = 0;
					while(isset($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . TEMPLATES_TABLE . '_' . $i]) && !empty($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . TEMPLATES_TABLE . '_' . $i])){
						$weAcResult = $weAcQuery->getItemById($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . TEMPLATES_TABLE . '_' . $i], TEMPLATES_TABLE, array("IsFolder"));
						if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
							$isAcError = true;
							break;
						}
						$i++;
					}
					$i = 0;
					while(isset($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . NAVIGATION_TABLE . '_' . $i]) && !empty($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . NAVIGATION_TABLE . '_' . $i])){
						$weAcResult = $weAcQuery->getItemById($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . NAVIGATION_TABLE . '_' . $i], NAVIGATION_TABLE, array("IsFolder"));
						if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
							$isAcError = true;
							break;
						}
						$i++;
					}
					if(defined('OBJECT_FILES_TABLE')){
						while(isset($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . OBJECT_FILES_TABLE . '_' . $i]) && !empty($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . OBJECT_FILES_TABLE . '_' . $i])){
							$weAcResult = $weAcQuery->getItemById($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . OBJECT_FILES_TABLE . '_' . $i], OBJECT_FILES_TABLE, array("IsFolder"));
							if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
								$isAcError = true;
								break;
							}
							$i++;
						}
					}

					if(defined('NEWSLETTER_TABLE')){
						while(isset($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . NEWSLETTER_TABLE . '_' . $i]) && !empty($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . NEWSLETTER_TABLE . '_' . $i])){
							$weAcResult = $weAcQuery->getItemById($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . NEWSLETTER_TABLE . '_' . $i], NEWSLETTER_TABLE, array("IsFolder"));
							if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
								$isAcError = true;
								break;
							}
							$i++;
						}
					}

					if($isAcError){
						print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_users', "[workspaceFieldError]"), we_message_reporting::WE_MESSAGE_ERROR));
						break;
					}
					$user_object = new we_users_user();
					$user_object->setState($_SESSION["user_session_data"]);

					if(!permissionhandler::hasPerm("ADMINISTRATOR") && $user_object->checkPermission("ADMINISTRATOR")){
						print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
						$user_object = new we_users_user();
						break;
					}
					$oldperm = $user_object->checkPermission("ADMINISTRATOR");
					if($user_object){

						if(!permissionhandler::hasPerm("SAVE_USER") && ($user_object->Type == we_users_user::TYPE_USER || $user_object->Type == we_users_user::TYPE_ALIAS) && $user_object->ID != 0){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}
						if(!permissionhandler::hasPerm("NEW_USER") && ($user_object->Type == we_users_user::TYPE_USER || $user_object->Type == we_users_user::TYPE_ALIAS) && $user_object->ID == 0){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}
						if(!permissionhandler::hasPerm("SAVE_GROUP") && $user_object->Type == we_users_user::TYPE_USER_GROUP && $user_object->ID != 0){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}
						if(!permissionhandler::hasPerm("NEW_GROUP") && $user_object->Type == we_users_user::TYPE_USER_GROUP && $user_object->ID == 0){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}
						if(isset($_REQUEST['oldtab'])){
							$user_object->preserveState(intval($_REQUEST['oldtab']), $_REQUEST['old_perm_branch']);
						}

						$id = $user_object->ID;
						if($user_object->username == '' && $user_object->Type != we_users_user::TYPE_ALIAS){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_users', "[username_empty]"), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}

						if($user_object->Alias == 0 && $user_object->Type == we_users_user::TYPE_ALIAS){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_users', "[username_empty]"), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}
						$exist = (f('SELECT 1 FROM ' . USER_TABLE . ' WHERE ID!=' . intval($user_object->ID) . " AND username='" . $user_object->username . "'", '', $GLOBALS['DB_WE']) == '1');
						if($exist && $user_object->Type != we_users_user::TYPE_ALIAS){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(sprintf(g_l('modules_users', "[username_exists]"), $user_object->username), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}
						if(($oldperm) && (!$user_object->checkPermission("ADMINISTRATOR")) && ($user_object->isLastAdmin())){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_users', "[modify_last_admin]"), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}

						$foo = ($user_object->ID ?
								getHash('SELECT ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($user_object->ID), $user_object->DB_WE) :
								array('ParentID' => 0));

						$ret = $user_object->saveToDB();
						$_SESSION['user_session_data'] = $user_object->getState();

						//	Save seem_startfile to DB when needed.
						if(isset($_REQUEST['seem_start_file'])){
							if(($_REQUEST['seem_start_file'] && $_REQUEST['seem_start_file'] != 0) || (isset($_SESSION['save_user_seem_start_file'][$_REQUEST["uid"]]))){
								$tmp = new DB_WE();

								if(isset($_REQUEST['seem_start_file'])){
									//	save seem_start_file from REQUEST
									$seem_start_file = $_REQUEST["seem_start_file"];
									if($user_object->ID == $_SESSION['user']['ID']){ // change preferences if user edits his own startfile
										$_SESSION['prefs']['seem_start_file'] = $seem_start_file;
									}
								} else {
									//	Speichere seem_start_file aus SESSION
									$seem_start_file = $_SESSION['save_user_seem_start_file'][$_REQUEST["uid"]];
								}

								$tmp->query('REPLACE INTO ' . PREFS_TABLE . ' SET userID=' . intval($_REQUEST['uid']) . ',`key`="seem_start_file",`value`="' . $tmp->escape($seem_start_file) . '"');
								unset($tmp);
								unset($seem_start_file);
								if(isset($_SESSION['save_user_seem_start_file'][$_REQUEST["uid"]])){
									unset($_SESSION['save_user_seem_start_file'][$_REQUEST["uid"]]);
								}
							}
						}

						if($ret == we_users_user::ERR_USER_PATH_NOK){
							echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_users', "[user_path_nok]"), we_message_reporting::WE_MESSAGE_ERROR));
						} else {
							$tree_code = ($id ?
									'top.content.updateEntry(' . $user_object->ID . ',' . $user_object->ParentID . ',"' . $user_object->Text . '",' . ($user_object->checkPermission('ADMINISTRATOR') ? 1 : 0) . ',' . ($user_object->LoginDenied ? 1 : 0) . ');' :
									'top.content.makeNewEntry("user.gif",' . $user_object->ID . ',' . $user_object->ParentID . ',"' . $user_object->Text . '",false,"' . (($user_object->Type == we_users_user::TYPE_USER_GROUP) ? ("folder") : (($user_object->Type == we_users_user::TYPE_ALIAS) ? ("alias") : ("user"))) . '","' . USER_TABLE . '",' . ($user_object->checkPermission("ADMINISTRATOR") ? 1 : 0) . ',' . ($user_object->LoginDenied ? 1 : 0) . ');');

							switch($user_object->Type){
								case we_users_user::TYPE_ALIAS:
									$savemessage = we_message_reporting::getShowMessageCall(sprintf(g_l('modules_users', "[alias_saved_ok]"), $user_object->Text), we_message_reporting::WE_MESSAGE_NOTICE);
									break;
								case we_users_user::TYPE_USER_GROUP:
									$savemessage = we_message_reporting::getShowMessageCall(sprintf(g_l('modules_users', "[group_saved_ok]"), $user_object->Text), we_message_reporting::WE_MESSAGE_NOTICE);
									break;
								case we_users_user::TYPE_USER:
								default:
									$savemessage = we_message_reporting::getShowMessageCall(sprintf(g_l('modules_users', "[user_saved_ok]"), $user_object->Text), we_message_reporting::WE_MESSAGE_NOTICE);
									break;
							}

							if($user_object->Type == we_users_user::TYPE_USER){
								$tree_code .= 'top.content.cgroup=' . $user_object->ParentID . ';';
							}
							echo we_html_element::jsElement('top.content.usetHot();' . $tree_code . $savemessage . $ret);
						}
					}
					break;
				case "delete_user":
					if(isset($_SESSION["user_session_data"]) && $_SESSION["user_session_data"]){
						$user_object = new we_users_user();
						$user_object->setState($_SESSION["user_session_data"]);

						if($user_object->ID == $_SESSION["user"]["ID"]){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_users', "[delete_user_same]"), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}

						if(we_users_util::isUserInGroup($_SESSION["user"]["ID"], $user_object->ID)){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_users', "[delete_group_user_same]"), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}

						if(!permissionhandler::hasPerm("ADMINISTRATOR") && $user_object->checkPermission("ADMINISTRATOR")){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
							$user_object = new we_users_user();
							break;
						}
						if(!permissionhandler::hasPerm("DELETE_USER") && $user_object->Type == we_users_user::TYPE_USER){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}
						if(!permissionhandler::hasPerm("DELETE_GROUP") && $user_object->Type == we_users_user::TYPE_USER_GROUP){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}

						if(isset($GLOBALS["user"]) && $user_object->Text == $GLOBALS["user"]["Username"]){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[user_same]"), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}

						if($user_object->checkPermission("ADMINISTRATOR")){
							if($user_object->isLastAdmin()){
								print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_users', "[modify_last_admin]"), we_message_reporting::WE_MESSAGE_ERROR));
								exit();
							}
						}

						switch($user_object->Type){
							case we_users_user::TYPE_USER_GROUP:
								$question = sprintf(g_l('modules_users', "[delete_alert_group]"), $user_object->Text);
								break;
							case we_users_user::TYPE_ALIAS:
								$question = sprintf(g_l('modules_users', "[delete_alert_alias]"), $user_object->Text);
								break;
							case we_users_user::TYPE_USER:
							default:
								$question = sprintf(g_l('modules_users', "[delete_alert_user]"), $user_object->Text);
								break;
						}
						print we_html_element::jsElement('
		if(confirm("' . $question . '")){
			top.content.cmd.location="' . $this->frameset . '?pnt=cmd&ucmd=do_delete";
		}');
					}
					break;
				case 'do_delete':
					if($_SESSION['user_session_data']){
						$user_object = new we_users_user();
						$user_object->setState($_SESSION["user_session_data"]);
						if(!permissionhandler::hasPerm('DELETE_USER') && $user_object->Type == we_users_user::TYPE_USER){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}
						if(!permissionhandler::hasPerm('DELETE_GROUP') && $user_object->Type == we_users_user::TYPE_USER_GROUP){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}
						if($user_object->deleteMe()){
							print we_html_element::jsElement('
		top.content.deleteEntry(' . $user_object->ID . ');
		top.content.editor.edheader.location="' . WEBEDITION_DIR . 'html/grayWithTopLine.html";
		top.content.editor.edbody.location="' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=mod_home&mod=users";
		top.content.editor.edfooter.location="' . WEBEDITION_DIR . 'html/gray.html";');
							unset($_SESSION["user_session_data"]);
						}
					}
					break;

				case 'check_user_display':
					if($_REQUEST['uid']){
						$mpid = f("SELECT ParentID FROM " . USER_TABLE . " WHERE ID=" . intval($_SESSION["user"]["ID"]), 'ParentID', $this->db);
						$pid = f("SELECT ParentID FROM " . USER_TABLE . " WHERE ID=" . intval($_REQUEST["uid"]), 'ParentID', $this->db);

						$search = true;
						$found = false;
						$first = true;

						while($search){
							if($mpid == $pid){
								$search = false;
								if(!$first){
									$found = true;
								}
							}
							$first = false;
							if($pid == 0){
								$search = false;
							}
							$pid = intval(f('SELECT ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($pid), 'ParentID', $this->db));
						}

						print we_html_element::jsElement(
								($found || permissionhandler::hasPerm('ADMINISTRATOR') ?
									'top.content.we_cmd(\'display_user\',' . $_REQUEST["uid"] . ')' :
									we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR)
						));
					}
					break;
			}
		}
	}

	function processVariables(){
		if(isset($_SESSION['weS']['raw_session'])){
			$this->raw = unserialize($_SESSION['weS']['raw_session']);
		}

		if(isset($this->raw) && is_array($this->raw->persistent_slots)){
			foreach($this->raw->persistent_slots as $key => $val){
				$varname = $val;
				if(isset($_REQUEST[$varname])){
					$this->raw->{$val} = $_REQUEST[$varname];
				}
			}
		}

		if(isset($_REQUEST['page']))
			if(isset($_REQUEST['page'])){
				$this->page = $_REQUEST['page'];
			}
	}

	function new_array_splice(&$a, $start, $len = 1){
		$ks = array_keys($a);
		$k = array_search($start, $ks);
		if($k !== false){
			$ks = array_splice($ks, $k, $len);
			foreach($ks as $k)
				unset($a[$k]);
		}
	}

}
