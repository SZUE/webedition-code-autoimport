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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');


//	we need some different functions for normal mode and seeMode
//	these function all have the same name: pWebEditionXXXX() and
//	are located in 2 different files. Depending on mode the correct
//	file is included and the matching functions are included.

if(!isset($_SESSION['weS']['we_mode']) || $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){ //	working in normal mode
	include_once(WE_INCLUDES_PATH . 'webEdition_normal.inc.php');
} else if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){ //	working in super-easy-edit-mode
	include_once(WE_INCLUDES_PATH . 'webEdition_seem.inc.php');
}


//	check session
we_html_tools::protect(null, WEBEDITION_DIR . 'index.php');

we_base_file::cleanTempFiles();
/* $sn = SERVER_NAME;

  if(strstr($sn, '@')) {
  list($foo,$sn) = explode('@',$sn);
  }
 */
//	unlock everything old, when a new window is opened.
if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) != "edit_include_document"){
	$GLOBALS['DB_WE']->query('DELETE FROM ' . LOCK_TABLE . '	WHERE lockTime<NOW()');
}
$GLOBALS['DB_WE']->query('UPDATE ' . USER_TABLE . '	SET Ping=0 WHERE Ping<UNIX_TIMESTAMP(NOW()-' . (we_base_constants::PING_TIME + we_base_constants::PING_TOLERANZ) . ')');

echo we_html_tools::getHtmlTop('webEdition - ' . $_SESSION['user']['Username']) .
 STYLESHEET .
 we_html_element::jsScript(JS_DIR . 'windows.js') .
 we_html_element::jsScript(JS_DIR . 'weTinyMceDialogs.js') .
 we_html_element::jsScript(JS_DIR . 'weNavigationHistory.php') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/yahoo-min.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/event-min.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/connection-min.js') .
 we_html_element::jsScript(JS_DIR . 'keyListener.js') .
 we_html_element::jsScript(JS_DIR . 'messageConsole.js') .
 we_html_element::jsScript(JS_DIR . 'we_showMessage.js') .
 we_message_reporting::jsString();

if(permissionhandler::hasPerm("CAN_SEE_DOCUMENTS")){
	$_table_to_load = FILE_TABLE;
} else if(permissionhandler::hasPerm("CAN_SEE_TEMPLATES")){
	$_table_to_load = TEMPLATES_TABLE;
} else if(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTFILES")){
	$_table_to_load = OBJECT_FILES_TABLE;
} else if(defined('OBJECT_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTS")){
	$_table_to_load = OBJECT_TABLE;
} else {
	$_table_to_load = "";
}

$jsCmd = array();
foreach($GLOBALS['_we_active_integrated_modules'] as $mod){
	if(file_exists(WE_MODULES_PATH . $mod . '/we_webEditionCmd_' . $mod . '.js')){
		$jsCmd[$mod] = WE_MODULES_DIR . $mod . '/we_webEditionCmd_' . $mod . '.js';
	}
}
?>

<script type="text/javascript"><!--
	self.focus();
	var Header = null;
	var Tree = null;
	var Vtabs = null;
	var TreeInfo = null;
	var busy = 0;
	var balken = null;
	var firstLoad = false;
	var hot = 0;
	var last = 0;
	var lastUsedLoadFrame = null;
	var nlHTMLMail = 0;
	var browserwind = null;
	var makefocus = null;
	var weplugin_wait = null;
	// is set in headermenu.php
	var weSidebar = null;
	// seeMode
	var seeMode = <?php echo ($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE) ? "true" : "false"; ?>; // in seeMode
	var seeMode_edit_include = <?php echo (isset($SEEM_edit_include) && $SEEM_edit_include) ? "true" : "false"; ?>; // in edit_include mode of seeMode

	var wePerms = {
		"ADMINISTRATOR": <?php echo permissionhandler::hasPerm("ADMINISTRATOR") ? 1 : 0; ?>,
		"DELETE_DOCUMENT": <?php echo permissionhandler::hasPerm("DELETE_DOCUMENT") ? 1 : 0; ?>,
		"DELETE_TEMPLATE": <?php echo permissionhandler::hasPerm("DELETE_TEMPLATE") ? 1 : 0; ?>,
		"DELETE_OBJECT": <?php echo permissionhandler::hasPerm("DELETE_OBJECT") ? 1 : 0; ?>,
		"DELETE_OBJECTFILE": <?php echo permissionhandler::hasPerm("DELETE_OBJECTFILE") ? 1 : 0; ?>,
		"DELETE_DOC_FOLDER": <?php echo permissionhandler::hasPerm("DELETE_DOC_FOLDER") ? 1 : 0; ?>,
		"DELETE_TEMP_FOLDER": <?php echo permissionhandler::hasPerm("DELETE_TEMP_FOLDER") ? 1 : 0; ?>
	};
	var g_l = {
		'unable_to_call_setpagenr': "<?php echo g_l('global', '[unable_to_call_setpagenr]'); ?>",
		'open_link_in_SEEM_edit_include': '<?php echo we_message_reporting::prepareMsgForJS(g_l('SEEM', '[open_link_in_SEEM_edit_include]')); ?>',
		'browser_crashed': '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[browser_crashed]')); ?>',
		'no_perms_action': '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[no_perms_action]')); ?>',
		'no_document_opened': '<?php echo we_message_reporting::prepareMsgForJS(g_l('global', '[no_document_opened]')); ?>',
		'no_editor_left': "<?php echo g_l('multiEditor', '[no_editor_left]'); ?>",
		'eplugin_exit_doc': "<?php echo g_l('alert', '[eplugin_exit_doc]'); ?>"
	};
	var userID =<?php echo $_SESSION["user"]["ID"]; ?>;
	var sess_id = "<?php echo session_id(); ?>";
	var size = {
		'tree': {
			'hidden':<?php echo weTree::HiddenWidth; ?>,
			'default':<?php echo weTree::DefaultWidth; ?>,
			'min':<?php echo weTree::MinWidth; ?>,
			'moveWidth':<?php echo weTree::MoveWidth; ?>,
			'deleteWidth':<?php echo weTree::DeleteWidth; ?>

		},
		'catSelect': {
			'width':<?php echo we_selector_file::WINDOW_CATSELECTOR_WIDTH; ?>,
			'height':<?php echo we_selector_file::WINDOW_CATSELECTOR_HEIGHT; ?>
		},
		'docSelect': {
			'width':<?php echo we_selector_file::WINDOW_DOCSELECTOR_WIDTH; ?>,
			'height':<?php echo we_selector_file::WINDOW_DOCSELECTOR_HEIGHT; ?>
		},
		'windowSelect': {
			'width':<?php echo we_selector_file::WINDOW_SELECTOR_WIDTH; ?>,
			'height':<?php echo we_selector_file::WINDOW_SELECTOR_HEIGHT; ?>
		},
		'windowDirSelect': {
			'width':<?php echo we_selector_file::WINDOW_DIRSELECTOR_WIDTH; ?>,
			'height':<?php echo we_selector_file::WINDOW_DIRSELECTOR_HEIGHT; ?>
		},
		'windowDelSelect': {
			'width':<?php echo we_selector_file::WINDOW_DELSELECTOR_WIDTH; ?>,
			'height':<?php echo we_selector_file::WINDOW_DELSELECTOR_HEIGHT; ?>
		},
	};
	var tables = {
		'FILE_TABLE': "<?php echo FILE_TABLE; ?>",
		'TEMPLATES_TABLE': "<?php echo TEMPLATES_TABLE; ?>",
		'OBJECT_FILES_TABLE': "<?php echo defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'; ?>",
		'OBJECT_TABLE': "<?php echo defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'; ?>",
		'CATEGORY_TABLE': "<?php echo CATEGORY_TABLE; ?>",
		'table_to_load': "<?php echo $_table_to_load; ?>"
	};

	var dirs = {
		"WE_SHOP_MODULE_DIR": "<?php echo defined('WE_SHOP_MODULE_DIR')?WE_SHOP_MODULE_DIR:''; ?>"
	};

	var SEEMODE =<?php echo intval($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE); ?>;
	var specialUnload =<?php echo intval(!(we_base_browserDetect::isChrome() || we_base_browserDetect::isSafari())); ?>;

	/*##################### messaging function #####################*/

	// this variable contains settings how to deal with settings
	// it has to be set when changing the preferences
	/**
	 * setting integer, any sum of 1,2,4
	 */
	var messageSettings = <?php echo (isset($_SESSION["prefs"]["message_reporting"]) && $_SESSION["prefs"]["message_reporting"] > 0 ? we_message_reporting::WE_MESSAGE_ERROR | $_SESSION["prefs"]["message_reporting"] : (we_message_reporting::WE_MESSAGE_ERROR | we_message_reporting::WE_MESSAGE_WARNING | we_message_reporting::WE_MESSAGE_NOTICE)); ?>;
	var weEditorWasLoaded = false;
	function reload_weJsStrings(newLng) {
		if (!newLng) {
			newLng = "<?php echo $GLOBALS['WE_LANGUAGE'] ?>";
		}
		var newSrc = "<?php echo JS_DIR; ?>weJsStrings.php?lng=" + newLng;
		var elem = document.createElement("script");
		elem.src = newSrc;
		document.getElementsByTagName("head")[0].appendChild(elem);
	}

	var setPageNrCallback = {
		success: function (o) {
		},
		failure: function (o) {
			alert(g_l.unable_to_call_setpagenr);
		}
	};
	if (self.location !== top.location) {
		top.location = self.location;
	}

<?php
if(defined('MESSAGING_SYSTEM')){
	?>
		function msg_update() {
			try {
				var fo = false;
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if((jsWindow" + k + "Object.ref=='edit_module') && !jsWindow" + k + "Object.wind.closed && (typeof(jsWindow" + k + "Object.wind.content.update_messaging) == 'function')){ jsWindow" + k + "Object.wind.content.update_messaging(); fo=true}");
					if (fo)
						break;
				}
			} catch (e) {

			}
		}
	<?php
}
?>

	function setTreeArrow(direction) {
		try {
			self.rframe.document.getElementById("arrowImg").src = "<?php echo BUTTONS_DIR; ?>icons/direction_" + direction + ".gif";
			if (direction === "right") {
				self.rframe.document.getElementById("incBaum").style.backgroundColor = "gray";
				self.rframe.document.getElementById("decBaum").style.backgroundColor = "gray";
			} else {
				self.rframe.document.getElementById("incBaum").style.backgroundColor = "";
				self.rframe.document.getElementById("decBaum").style.backgroundColor = "";
			}
		} catch (e) {
			// Nothing
			;
		}
	}

	function we_cmd() {
		var hasPerm = false;
		var url = "/webEdition/we_cmd.php?";
		for (var i = 0; i < arguments.length; i++) {
			url += "we_cmd[" + i + "]=" + encodeURIComponent(arguments[i]);
			if (i < (arguments.length - 1))
				url += "&";
		}

		if (window.screen) {
			h = ((screen.height - 100) > screen.availHeight) ? screen.height - 100 : screen.availHeight;
			w = screen.availWidth;
		}

		//	When coming from a we_cmd, always mark the document as opened with we !!!!
		if (top.weEditorFrameController && top.weEditorFrameController.getActiveDocumentReference) {
			try {
				var _string = ',edit_document,new_document,open_extern_document,edit_document_with_parameters,new_folder,edit_folder';
				if (_string.indexOf("," + arguments[0] + ",") === -1) {
					top.weEditorFrameController.getActiveDocumentReference().openedWithWE = true;
				}
			} catch (exp) {

			}
		}
		switch (arguments[0]) {
			case "exit_modules":
				if (jsWindow_count) {
					for (i = 0; i < jsWindow_count; i++) {
						eval("if(jsWindow" + i + "Object.ref=='edit_module') jsWindow" + i + "Object.close()");
					}
				}
				break;
<?php
//	In we.inc.php all names of the installed modules have already been searched
//	so we only have to use the array $GLOBALS['_we_active_integrated_modules']
ob_start();
foreach($GLOBALS['_we_active_integrated_modules'] as $mod){
	if(file_exists(WE_MODULES_PATH . $mod . '/we_webEditionCmd_' . $mod . '.inc.php')){
		include(WE_MODULES_PATH . $mod . '/we_webEditionCmd_' . $mod . '.inc.php');
	}
}

if(($_jsincludes = we_tool_lookup::getJsCmdInclude())){
	foreach($_jsincludes as $_jsinclude){
		include($_jsinclude);
	}
}
$modSwitch = str_replace(
	array_merge(explode("\n", we_html_element::jsElement()), array(
	'switch (WE_REMOVE) {',
	'switch(WE_REMOVE){',
	'switch(WE_REMOVE) {',
	'switch (WE_REMOVE){',
	'}//WE_REMOVE'
		)
	), '', ob_get_clean()
);

echo $modSwitch; // deal with not activated modules

foreach(array_diff(array_keys(we_base_moduleInfo::getAllModules()), $GLOBALS['_we_active_integrated_modules']) as $m){
	echo 'case "' . $m . '_edit_ifthere":
';
}
echo 'new jsWindow(url,"module_info",-1,-1,380,250,true,true,true);
		break;';
?>
			case "openFirstStepsWizardMasterTemplate":
			case "openFirstStepsWizardDetailTemplates":
				new jsWindow(url, "we_firststepswizard", -1, -1, 1024, 768, true, true, true);
				break;
			case "openUnpublishedObjects":
				we_cmd("tool_weSearch_edit", "", "", 7, 3);
				break;
			case "openUnpublishedPages":
				we_cmd("tool_weSearch_edit", "", "", 4, 3);
				break;
			case "openCatselector":
				new jsWindow(url, "we_cateditor", -1, -1, size.catSelect.width, size.catSelect.height, true, true, true, true);
				break;
			case "openSidebar":
				top.weSidebar.open("default");
				break;
			case "loadSidebarDocument":
				top.rframe.weSidebarContent.location.href = url;
				break;
			case "versions_preview":
				new jsWindow(url, "version_preview", -1, -1, 1000, 750, true, false, true, false);
				break;
			case "versions_wizard":
				new jsWindow(url, "versions_wizard", -1, -1, 600, 620, true, false, true);
				break;
			case "versioning_log":
				new jsWindow(url, "versioning_log", -1, -1, 600, 500, true, false, true);
				break;
			case "delete_single_document_question":
				var cType = top.weEditorFrameController.getActiveEditorFrame().getEditorContentType();
				var eTable = top.weEditorFrameController.getActiveEditorFrame().getEditorEditorTable();
				var path = top.weEditorFrameController.getActiveEditorFrame().getEditorDocumentPath();

				toggleBusy(1);
				if (weEditorFrameController.getActiveDocumentReference()) {
					if (!hasPermDelete(eTable, (cType === "folder"))) {
						top.we_showMessage(g_l.no_perms_action, WE_MESSAGE_ERROR, window);
					} else if (window.confirm("<?php echo g_l('alert', '[delete_single][confirm_delete]'); ?>\n" + path)) {
						url2 = url.replace(/we_cmd\[0\]=delete_single_document_question/g, "we_cmd[0]=delete_single_document");
						submit_we_form(top.weEditorFrameController.getActiveDocumentReference().frames["3"], self.load, url2 + "&we_cmd[2]=" + top.weEditorFrameController.getActiveEditorFrame().getEditorEditorTable());
					}
				} else {
					top.we_showMessage(g_l.no_document_opened, WE_MESSAGE_ERROR, window);
				}
				break;
			case "delete_single_document":
				var cType = top.weEditorFrameController.getActiveEditorFrame().getEditorContentType();
				var eTable = top.weEditorFrameController.getActiveEditorFrame().getEditorEditorTable();

				toggleBusy(1);
				if (weEditorFrameController.getActiveDocumentReference()) {
					if (!hasPermDelete(eTable, (cType === "folder"))) {
						top.we_showMessage(g_l.no_perms_action, WE_MESSAGE_ERROR, window);
					} else {
						submit_we_form(top.weEditorFrameController.getActiveDocumentReference().frames["3"], self.load, url + "&we_cmd[2]=" + top.weEditorFrameController.getActiveEditorFrame().getEditorEditorTable());
					}
				} else {
					top.we_showMessage(g_l.no_document_opened, WE_MESSAGE_ERROR, window);
				}
				break;
			case "do_delete":
				toggleBusy(1);
				submit_we_form(self.rframe.treeheader, self.load, url);
				//we_sbmtFrmC(self.load,url);
				break;
			case "move_single_document":
				toggleBusy(1);
				submit_we_form(top.weEditorFrameController.getActiveDocumentReference().frames["3"], self.load, url);
				break;
			case "do_move":
				toggleBusy(1);
				submit_we_form(self.rframe.treeheader, self.load, url);
				//we_sbmtFrmC(self.load,url);
				break;
			case "open_document":
				we_cmd("load", tables.FILE_TABLE);
				url = "/webEdition/we_cmd.php?we_cmd[0]=openDocselector&we_cmd[2]=" + tables.FILE_TABLE + "&we_cmd[5]=<?php echo rawurlencode("opener.top.weEditorFrameController.openDocument(table,currentID,currentType)"); ?>&we_cmd[9]=1";
				new jsWindow(url, "we_dirChooser", -1, -1, size.docSelect.width, size.docSelect.height, true, true, true, true);
				break;
			case "open_template":
				we_cmd("load", tables.TEMPLATES_TABLE);
				url = "/webEdition/we_cmd.php?we_cmd[0]=openDocselector&we_cmd[8]=<?php echo we_base_ContentTypes::TEMPLATE; ?>&we_cmd[2]=<?php echo TEMPLATES_TABLE; ?>&we_cmd[5]=<?php echo rawurlencode("opener.top.weEditorFrameController.openDocument(table,currentID,currentType)"); ?>&we_cmd[9]=1";
				new jsWindow(url, "we_dirChooser", -1, -1, size.docSelect.width, size.docSelect.height, true, true, true, true);
				break;
			case "change_passwd":
				new jsWindow(url, "we_change_passwd", -1, -1, 250, 220, true, false, true, false);
				break;
			case "update":
				new jsWindow("/webEdition/liveUpdate/liveUpdate.php?active=update", "we_update_" + sess_id, -1, -1, 600, 500, true, true, true);
				break;
			case "upgrade":
				new jsWindow("/webEdition/liveUpdate/liveUpdate.php?active=upgrade", "we_update_" + sess_id, -1, -1, 600, 500, true, true, true);
				break;
				/*case "moduleinstallation":
				 new jsWindow("<?php echo WEBEDITION_DIR; ?>liveUpdate/liveUpdate.php?active=modules", "we_update_<?php echo session_id(); ?>", -1, -1, 600, 500, true, true, true);
				 break;*/
			case "languageinstallation":
				new jsWindow("/webEdition/liveUpdate/liveUpdate.php?active=languages", "we_update_" + sess_id, -1, -1, 600, 500, true, true, true);
				break;
			case "del":
				we_cmd('delete', 1, arguments[2]);
				treeData.setstate(treeData.tree_states["select"]);
				top.treeData.unselectnode();
				top.drawTree();
				break;
			case "mv":
				we_cmd('move', 1, arguments[2]);
				treeData.setstate(treeData.tree_states["selectitem"]);
				top.treeData.unselectnode();
				top.drawTree();
				break;
			case "changeLanguageRecursive":
			case "changeTriggerIDRecursive":
				we_repl(self.load, url, arguments[0]);
				break;
			case "logout":
				we_repl(self.load, url, arguments[0]);
				break;
			case "dologout":
				// before the command 'logout' is executed, ask if unsaved changes should be saved
				if (top.weEditorFrameController.doLogoutMultiEditor()) {
					regular_logout = true;
					we_cmd('logout');
				}
				break;
			case "exit_multi_doc_question":
				new jsWindow(url, "exit_multi_doc_question", -1, -1, 500, 300, true, false, true);
				break;
			case "loadFolder":
			case "closeFolder":
				we_repl(self.load, url, arguments[0]);
				break;
			case "reload_editfooter":
				we_repl(top.weEditorFrameController.getActiveDocumentReference().frames[3], url, arguments[0]);
				break;
			case "rebuild":
				new jsWindow(url, "rebuild", -1, 0, 609, 645, true, false, true);
				break;
			case "openPreferences":
				new jsWindow(url, "preferences", -1, -1, 540, 670, true, true, true, true);
				break;
			case "editCat":
				we_cmd("openCatselector", 0, tables.CATEGORY_TABLE, "", "", "", "", "", 1);
				break;
			case "editThumbs":
				new jsWindow(url, "thumbnails", -1, -1, 500, 550, true, true, true);
				break;
			case "editMetadataFields":
				new jsWindow(url, "metadatafields", -1, -1, 500, 550, true, true, true);
				break;
			case "doctypes":
				new jsWindow(url, "doctypes", -1, -1, 800, 670, true, true, true);
				break;
			case "info":
				new jsWindow(url, "info", -1, -1, 432, 360, true, false, true);
				break;
			case "webEdition_online":
				new jsWindow("http://www.webedition.org/", "webEditionOnline", -1, -1, 960, 700, true, true, true, true);
				break;
			case "snippet_shop":
				alert("Es gibt noch keine URL für die Snippets Seite");
				break;
			case "help_modules":
				var fo = false;
				if (jsWindow_count) {
					for (var k = jsWindow_count - 1; k > -1; k--) {
						eval("if(jsWindow" + k + "Object.ref=='edit_module'){ fo=true;wind=jsWindow" + k + "Object.wind}");
						if (fo) {
							break;
						}
					}
					wind.focus();
				}
				url = "/webEdition/getHelp.php";
				new jsWindow(url, "help", -1, -1, 800, 600, true, false, true, true);
				break;
			case "info_modules":
				var fo = false;
				if (jsWindow_count) {
					for (var k = jsWindow_count - 1; k > -1; k--) {
						eval("if(jsWindow" + k + "Object.ref=='edit_module'){ fo=true;wind=jsWindow" + k + "Object.wind}");
						if (fo) {
							break;
						}
					}
					wind.focus();
				}
				url = "/webEdition/we_cmd.php?we_cmd[0]=info";
				new jsWindow(url, "info", -1, -1, 432, 350, true, false, true);
				break;
			case "help_tools":
				var fo = false;
				if (jsWindow_count) {
					for (var k = jsWindow_count - 1; k > -1; k--) {
						eval("if(jsWindow" + k + "Object.ref=='tool_window' || jsWindow" + k + "Object.ref=='tool_window_navigation' || jsWindow" + k + "Object.ref=='tool_window_weSearch'){ fo=true;wind=jsWindow" + k + "Object.wind}");
						if (fo) {
							break;
						}
					}
					wind.focus();
				}
				url = "/webEdition/getHelp.php";
				new jsWindow(url, "help", -1, -1, 800, 600, true, false, true, true);
				break;
			case "info_tools":
				var fo = false;
				if (jsWindow_count) {
					for (var k = jsWindow_count - 1; k > -1; k--) {
						eval("if(jsWindow" + k + "Object.ref=='tool_window' || jsWindow" + k + "Object.ref=='tool_window_navigation' || jsWindow" + k + "Object.ref=='tool_window_weSearch'){ fo=true;wind=jsWindow" + k + "Object.wind}");
						if (fo) {
							break;
						}
					}
					wind.focus();
				}
				url = "/webEdition/we_cmd.php?we_cmd[0]=info";
				new jsWindow(url, "info", -1, -1, 432, 350, true, false, true);
				break;
			case "help":
				url = "/webEdition/getHelp.php" + (arguments[1] ?
								"?hid=" + arguments[1] :
								""
								);
				new jsWindow(url, "help", -1, -1, 720, 600, true, false, true, true);
				break;
			case "help_documentation":
				new jsWindow("http://documentation.webedition.org/wiki/<?php echo ($GLOBALS["WE_LANGUAGE"] === 'Deutsch' ? 'de' : 'en'); ?>/", "help_documentation", -1, -1, 960, 700, true, true, true, true);
				break;
			case "help_forum":
				new jsWindow("http://forum.webedition.org", "help_forum", -1, -1, 960, 700, true, true, true, true);
				break;
			case "help_bugtracker":
				new jsWindow("http://qa.webedition.org/tracker/", "help_bugtracker", -1, -1, 960, 700, true, true, true, true);
				break;
			case "help_tagreference":
				new jsWindow("http://tags.webedition.org/<?php echo ($GLOBALS["WE_LANGUAGE"] === 'Deutsch' ? 'de' : 'en'); ?>/", "help_tagreference", -1, -1, 960, 700, true, true, true, true);
				break;
			case "help_demo":
				new jsWindow("http://demo.webedition.org/<?php echo ($GLOBALS["WE_LANGUAGE"] === 'Deutsch' ? 'de' : 'en'); ?>/", "help_demo", -1, -1, 960, 700, true, true, true, true);
				break;
			case "help_changelog":
				new jsWindow("http://www.webedition.org/de/webedition-cms/versionshistorie/webedition-6/", "help_changelog", -1, -1, 960, 700, true, true, true, true);
				break;
			case "openSelector":
				new jsWindow(url, "we_fileselector", -1, -1, size.windowSelect.width, size.windowSelect.height, true, true, true, true);
				break;
			case "openDirselector":
				new jsWindow(url, "we_fileselector", -1, -1, size.windowDirSelect.width, size.windowDirSelect.height, true, true, true, true);
				break;
			case "openDocselector":
				new jsWindow(url, "we_fileselector", -1, -1, size.docSelect.width, size.docSelect.height, true, true, true, true);
				break;
			case "setTab":
				if (self.Vtabs && self.Vtabs.setTab && (typeof treeData !== "undefined")) {
					self.Vtabs.setTab(arguments[1]);
					treeData.table = arguments[1];
				} else {
					setTimeout('we_cmd("setTab","' + arguments[1] + '")', 500);
				}
				break;
			case "showLoadInfo":
				we_repl(self.Tree, url, arguments[0]);
				break;
			case "update_image":
			case "update_file":
			case "copyDocument":
			case "insert_entry_at_list":
			case "edit_list":
			case "delete_list":
			case "down_entry_at_list":
			case "up_entry_at_list":
			case "down_link_at_list":
			case "up_link_at_list":
			case "add_entry_to_list":
			case "add_link_to_linklist":
			case "change_link":
			case "change_linklist":
			case "delete_linklist":
			case "insert_link_at_linklist":
			case "change_doc_type":
			case "doctype_changed":
			case "remove_image":
			case "delete_link":
			case "delete_cat":
			case "add_cat":
			case "delete_all_cats":
			case "schedule_add":
			case "schedule_del":
			case "schedule_add_schedcat":
			case "schedule_delete_all_schedcats":
			case "schedule_delete_schedcat":
			case "template_changed":
			case "add_navi":
			case "delete_navi":
			case "delete_all_navi":
				// set Editor hot
				_EditorFrame = top.weEditorFrameController.getActiveEditorFrame();
				_EditorFrame.setEditorIsHot(true);
			case "reload_editpage":
			case "wrap_on_off":
			case "restore_defaults":
			case "do_add_thumbnails":
			case "del_thumb":
			case "resizeImage":
			case "rotateImage":
			case "doImage_convertGIF":
			case "doImage_convertPNG":
			case "doImage_convertJPEG":
			case "doImage_crop":
			case "revert_published":

				// get editor root frame of active tab
				var _currentEditorRootFrame = top.weEditorFrameController.getActiveDocumentReference();
				// get visible frame for displaying editor page
				var _visibleEditorFrame = top.weEditorFrameController.getVisibleEditorFrame();
				// if cmd equals "reload_editpage" and there are parameters, attach them to the url
				if (arguments[0] === "reload_editpage" && _currentEditorRootFrame.parameters) {
					url += _currentEditorRootFrame.parameters;
				}

				// attach necessary parameters if available
				if (arguments[0] === "reload_editpage" && arguments[1]) {
					url += '#f' + arguments[1];
				} else if (arguments[0] === "remove_image" && arguments[2]) {
					url += '#f' + arguments[2];
				}

				// focus visible editor frame
				if (_visibleEditorFrame) {
					_visibleEditorFrame.focus();
				}

				if (_currentEditorRootFrame) {
					if (!we_sbmtFrm(_visibleEditorFrame, url, _visibleEditorFrame)) {
						if (arguments[0] !== "update_image") {
							// add we_transaction, if not set
							if (!arguments[2]) {
								arguments[2] = top.weEditorFrameController.getActiveEditorFrame().getEditorTransaction();
							}
							url += "&we_transaction=" + arguments[2];
						}
						we_repl(_visibleEditorFrame, url, arguments[0]);
					}
				}

				break;
			case "switch_edit_page":

				// get editor root frame of active tab
				var _currentEditorRootFrame = top.weEditorFrameController.getActiveDocumentReference();
				// get visible frame for displaying editor page
				var _visibleEditorFrame = top.weEditorFrameController.getVisibleEditorFrame();
				// frame where the form should be sent from
				var _sendFromFrame = _visibleEditorFrame;
				// set flag to true if active frame is frame nr 2 (frame for displaying editor page 1 with content editor)
				var _isEditpageContent = _visibleEditorFrame === _currentEditorRootFrame.frames[2];
				//var _isEditpageContent = _visibleEditorFrame == _currentEditorRootFrame.document.getElementsByTagName("div")[2].getElementsByTagName("iframe")[0];

				// if we switch from we_base_constants::WE_EDITPAGE_CONTENT to another page
				if (_isEditpageContent && arguments[1] !== <?php echo we_base_constants::WE_EDITPAGE_CONTENT; ?>) {
					// clean body to avoid flickering
					try {
						_currentEditorRootFrame.frames[1].document.body.innerHTML = "";
					} catch (e) {
						//can be caused by not loaded content
					}
					// switch to normal frame
					top.weEditorFrameController.switchToNonContentEditor();
					// set var to new active editor frame
					_visibleEditorFrame = _currentEditorRootFrame.frames[1];
					//_visibleEditorFrame = _currentEditorRootFrame.document.getElementsByTagName("div")[1].getElementsByTagName("iframe")[0];

					// set flag to false
					_isEditpageContent = false;
					// if we switch to we_base_constants::WE_EDITPAGE_CONTENT from another page
				} else if (!_isEditpageContent && arguments[1] === <?php echo we_base_constants::WE_EDITPAGE_CONTENT; ?>) {
					// switch to content editor frame
					top.weEditorFrameController.switchToContentEditor();
					// set var to new active editor frame
					_visibleEditorFrame = _currentEditorRootFrame.frames[2];
					//_visibleEditorFrame = _currentEditorRootFrame.document.getElementsByTagName("div")[2].getElementsByTagName("iframe")[0];
					// set flag to false
					_isEditpageContent = true;
				}

				// frame where the form should be sent to
				var _sendToFrame = _visibleEditorFrame;
				// get active transaction
				var _we_activeTransaction = top.weEditorFrameController.getActiveEditorFrame().getEditorTransaction();
				// if there are parameters, attach them to the url
				if (_currentEditorRootFrame.parameters) {
					url += _currentEditorRootFrame.parameters;
				}

				// focus the frame
				if (_sendToFrame) {
					_sendToFrame.focus();
				}
				// if visible frame equals to editpage content and there is already content loaded
				if (_isEditpageContent && typeof (_visibleEditorFrame.weIsTextEditor) !== "undefined" && _currentEditorRootFrame.frames[2].location !== "about:blank") {
					// tell the backend the right edit page nr and break (don't send the form)
					//YAHOO.util.Connect.setForm(_sendFromFrame.document.we_form);
					YAHOO.util.Connect.asyncRequest('POST', "/webEdition/rpc/rpc.php", setPageNrCallback, 'protocol=json&cmd=SetPageNr&transaction=' + _we_activeTransaction + "&editPageNr=" + arguments[1]);
					if (_visibleEditorFrame.reloadContent === false) {
						break;
					}
					_visibleEditorFrame.reloadContent = false;
				}


				if (_currentEditorRootFrame) {

					if (!we_sbmtFrm(_sendToFrame, url, _sendFromFrame)) {
						// add we_transaction, if not set
						if (!arguments[2]) {
							arguments[2] = _we_activeTransaction;
						}
						url += "&we_transaction=" + arguments[2];
						we_repl(_sendToFrame, url, arguments[0]);
					}
				}

				break;
			case "edit_document_with_parameters":
			case "edit_document":
				toggleBusy(1);
				try {
					if ((typeof treeData !== "undefined") && treeData) {
						treeData.unselectnode();
						if (arguments[1]) {
							treeData.selection_table = arguments[1];
						}
						if (arguments[2]) {
							treeData.selection = arguments[2];
						}
						if (treeData.selection_table === treeData.table) {
							treeData.selectnode(treeData.selection);
						}
					}
				} catch (e) {
				}

				if ((nextWindow = top.weEditorFrameController.getFreeWindow())) {
					_nextContent = nextWindow.getDocumentReference();
					// activate tab and set state to loading
					top.weMultiTabs.addTab(nextWindow.getFrameId(), nextWindow.getFrameId(), nextWindow.getFrameId());
					// use Editor Frame
					nextWindow.initEditorFrameData(
									{
										"EditorType": "model",
										"EditorEditorTable": arguments[1],
										"EditorDocumentId": arguments[2],
										"EditorContentType": arguments[3]
									}
					);
					// set Window Active and show it
					top.weEditorFrameController.setActiveEditorFrame(nextWindow.FrameId);
					top.weEditorFrameController.toggleFrames();
					if (_nextContent.frames && _nextContent.frames["1"]) {
						if (!we_sbmtFrm(_nextContent, url)) {
							we_repl(_nextContent, url + "&frameId=" + nextWindow.getFrameId());
						}
					} else {
						we_repl(_nextContent, url + "&frameId=" + nextWindow.getFrameId());
					}

				} else {
					alert(g_l.no_editor_left);
				}
				break;
			case "open_extern_document":
			case "new_document":
				if ((nextWindow = top.weEditorFrameController.getFreeWindow())) {
					_nextContent = nextWindow.getDocumentReference();
					// activate tab and set it status loading ...
					top.weMultiTabs.addTab(nextWindow.getFrameId(), nextWindow.getFrameId(), nextWindow.getFrameId());
					nextWindow.updateEditorTab();
					// set Window Active and show it
					top.weEditorFrameController.setActiveEditorFrame(nextWindow.getFrameId());
					top.weEditorFrameController.toggleFrames();
					// load new document editor
					we_repl(_nextContent, url + "&frameId=" + nextWindow.getFrameId());
				} else {
					alert(g_l.no_editor_left);
				}
				break;
			case "close_document":
				if (arguments[1]) { // close special tab
					top.weEditorFrameController.closeDocument(arguments[1]);
				} else if ((_currentEditor = top.weEditorFrameController.getActiveEditorFrame())) {
					// close active tab
					top.weEditorFrameController.closeDocument(_currentEditor.getFrameId());
				}
				break;
			case "close_all_documents":
				top.weEditorFrameController.closeAllDocuments();
				break;
			case "close_all_but_active_document":

				activeId = null;
				if (arguments[1]) {
					activeId = arguments[1];
				}
				top.weEditorFrameController.closeAllButActiveDocument(activeId);
				break;
			case "open_url_in_editor":
				we_repl(self.load, url, arguments[0]);
				break;
			case "publish":
			case "unpublish":
				toggleBusy(1);
				doPublish(url, arguments[1], arguments[0]);
				break;
			case "save_document":


				var _EditorFrame = top.weEditorFrameController.getActiveEditorFrame();
				if (_EditorFrame && _EditorFrame.getEditorFrameWindow().frames && _EditorFrame.getEditorFrameWindow().frames["1"]) {
					_EditorFrame.getEditorFrameWindow().frames["1"].focus();
				}

				toggleBusy(1);
				if (!arguments[1]) {
					arguments[1] = _EditorFrame.getEditorTransaction();
				}

				doSave(url, arguments[1], arguments[0]);
				break;
			case "exit_doc_question":
				// return !! important for multiEditor
				return new jsWindow(url, "exit_doc_question", -1, -1, 380, 130, true, false, true);
			case "openDelSelector":
				new jsWindow(url, "we_del_selector", -1, -1, size.windowDelSelect.width, size.windowDelSelect.height, true, true, true, true);
				break;
			case "browse":
				openBrowser();
				break;
			case "home":
				if (top.treeData) {
					top.treeData.unselectnode();
				}
				top.weEditorFrameController.openDocument('', '', '', 'open_cockpit');
				break;
			case "browse_server":
				new jsWindow(url, "browse_server", -1, -1, 840, 400, true, false, true);
				break;
			case "make_backup":
				new jsWindow(url, "export_backup", -1, -1, 680, 600, true, true, true);
				break;
			case "recover_backup":
				new jsWindow(url, "recover_backup", -1, -1, 680, 600, true, true, true);
				break;
			case "import_docs":
				new jsWindow(url, "import_docs", -1, -1, 480, 390, true, false, true);
				break;
			case "import":
				new jsWindow(url, "import", -1, -1, 600, 620, true, false, true);
				break;
			case "export":
				new jsWindow(url, "export", -1, -1, 600, 540, true, false, true);
				break;
			case "copyWeDocumentCustomerFilter":
				new jsWindow(url, "copyWeDocumentCustomerFilter", -1, -1, 400, 115, true, true, true);
				break;
			case "copyFolder":
				new jsWindow(url, "copyfolder", -1, -1, 550, 320, true, true, true);
				break;
			case "del_frag":
				new jsWindow("/webEdition/delFrag.php?currentID=" + arguments[1], "we_del", -1, -1, 600, 130, true, true, true);
				break;
			case "open_wysiwyg_window":
				if (top.weEditorFrameController.getActiveDocumentReference()) {
					top.weEditorFrameController.getActiveDocumentReference().openedWithWE = false;
				}
				var wyw = Math.max(arguments[2], arguments[9]);
				wyw = wyw ? wyw : 800;
				var wyh = parseInt(arguments[3]) + parseInt(arguments[10]);
				wyh = wyh ? wyh : 600;
				if (window.screen) {
					var screen_height = ((screen.height - 50) > screen.availHeight) ? screen.height - 50 : screen.availHeight;
					screen_height = screen_height - 40;
					var screen_width = screen.availWidth - 10;
					wyw = Math.min(screen_width, wyw);
					wyh = Math.min(screen_height, wyh);
				}
				// set new width & height
				url = url.replace(/we_cmd\[2\]=[^&]+/, 'we_cmd[2]=' + wyw);
				url = url.replace(/we_cmd\[3\]=[^&]+/, 'we_cmd[3]=' + (wyh - arguments[10]));
				new jsWindow(url, "we_wysiwygWin", -1, -1, Math.max(220, wyw + (document.all ? 0 : ((navigator.userAgent.toLowerCase().indexOf('safari') > -1) ? 20 : 4))), Math.max(100, wyh + 60), true, false, true);
				//doPostCmd(arguments,"we_wysiwygWin");
				break;
			case "not_installed_modules":
				we_repl(self.load, url, arguments[0]);
				break;
			case "start_multi_editor":
				we_repl(self.load, url, arguments[0]);
				break;
			case "customValidationService":
				new jsWindow(url, "we_customizeValidation", -1, -1, 700, 700, true, false, true);
				break;
			case "reset_home":

				var _currEditor = top.weEditorFrameController.getActiveEditorFrame();
				if (_currEditor && _currEditor.getEditorType() === "cockpit") {
					if (confirm('<?php echo g_l('alert', '[cockpit_reset_settings]'); ?>')) {
						//FIXME: currently this doesn't work
						top.weEditorFrameController.getActiveDocumentReference().location = '<?php echo WEBEDITION_DIR; ?>we/include/we_widgets/cmd.php?we_cmd[0]=' + arguments[0];
						if ((typeof treeData !== "undefined") && treeData) {
							treeData.unselectnode();
						}
					}
				} else {
<?php echo we_message_reporting::getShowMessageCall(g_l('alert', '[cockpit_not_activated]'), we_message_reporting::WE_MESSAGE_NOTICE); ?>
				}

				break;
			case "edit_home":
				if (arguments[1] === 'add') {
					self.load.location = '/webEdition/we/include/we_widgets/cmd.php?we_cmd[0]=' + arguments[1] + '&we_cmd[1]=' + arguments[2] + '&we_cmd[2]=' + arguments[3];
				}
				break;
			case "edit_navi":
				new jsWindow(url, "we_navieditor", -1, -1, 400, 360, true, true, true, true);
				break;
			case "new_widget_sct":
			case "new_widget_rss":
			case "new_widget_msg":
			case "new_widget_usr":
			case "new_widget_mfd":
			case "new_widget_upb":
			case "new_widget_mdc":
			case "new_widget_pad":
			case "new_widget_shp":
			case "new_widget_fdl":
				if (top.weEditorFrameController.getActiveDocumentReference() && top.weEditorFrameController.getActiveDocumentReference().quickstart) {
					top.weEditorFrameController.getActiveDocumentReference().createWidget(arguments[0].substr(arguments[0].length - 3), 1, 1);
				}
				else {
<?php echo we_message_reporting::getShowMessageCall(g_l('alert', '[cockpit_not_activated]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
				}
				break;
			case "initPlugin":
				weplugin_wait = new jsWindow("/webEdition/editors/content/eplugin/weplugin_wait.php?callback=" + arguments[1], "weplugin_wait", -1, -1, 300, 100, true, false, true);
				break;
			case "edit_settings_newsletter":
				new jsWindow("<?php echo WE_MODULES_DIR; ?>newsletter/edit_newsletter_frameset.php?pnt=newsletter_settings", "newsletter_settings", -1, -1, 600, 750, true, false, true);
				break;
			case "edit_settings_customer":
				new jsWindow("<?php echo WE_MODULES_DIR; ?>customer/edit_customer_frameset.php?pnt=settings", "customer_settings", -1, -1, 520, 300, true, false, true);
				break;
			case "edit_settings_shop":
<?php
if(defined('WE_SHOP_MODULE_DIR')){
	?>
					new jsWindow("<?php echo WE_SHOP_MODULE_DIR; ?>edit_shop_pref.php", "shoppref", -1, -1, 470, 600, true, false, true);
	<?php
}
?>
				break;
			case "edit_settings_messaging":
<?php
if(defined('WE_MESSAGING_MODULE_DIR')){
	?>
					new jsWindow("<?php echo WE_MESSAGING_MODULE_DIR; ?>messaging_settings.php?mode=1", "messaging_settings", -1, -1, 280, 200, true, false, true);
	<?php
}
?>
				break;
			case "edit_settings_spellchecker":
				we_cmd("spellchecker_edit");
				break;
			case "edit_settings_banner":
				we_cmd("banner_default");
				break;
			case "edit_settings_editor":
				if (top.plugin.editSettings) {
					top.plugin.editSettings();
				} else {
					we_cmd("initPlugin", "top.plugin.editSettings()");
				}
				break;
			case "edit_settings_glossary":
				we_cmd("glossary_settings");
				break;
			case "sysinfo":
				new jsWindow("/webEdition/sysinfo.php", "we_sysinfo", -1, -1, 720, 660, true, false, true);
				break;
			case "showerrorlog":
				new jsWindow("/webEdition/errorlog.php", "we_errorlog", -1, -1, 920, 660, true, false, true);
				break;
			case "view_backuplog":
				new jsWindow("/webEdition/backuplog.php", "we_backuplog", -1, -1, 720, 660, true, false, true);
				break;
			case "show_message_console":
				new jsWindow("/webEdition/we/include/jsMessageConsole/messageConsole.php", "we_jsMessageConsole", -1, -1, 600, 500, true, false, true, false);
				break;
			case "remove_from_editor_plugin":
				if (arguments[1] && top.plugin && top.plugin.remove) {
					top.plugin.remove(arguments[1]);
				}
				break;
			case "eplugin_exit_doc" :
				if (typeof (top.plugin) !== "undefined" && typeof (top.plugin.document.WePlugin) !== "undefined") {
					if (top.plugin.isInEditor(arguments[1])) {
						return confirm(g_l.eplugin_exit_doc);
					}
				}
				return true;
			case "editor_plugin_doc_count":
				if (typeof (top.plugin.document.WePlugin) !== "undefined") {
					return top.plugin.getDocCount();
				}
				return 0;
			case "open_tagreference":
				var docupath = "http://tags.webedition.org/<?php echo ($GLOBALS['WE_LANGUAGE'] === 'Deutsch') ? 'de' : 'en' ?>/" + arguments[1];
				new jsWindow(docupath, "we_tagreference", -1, -1, 1024, 768, true, true, true);
				break;
			case "new":
				if (SEEMODE) {
					top.weEditorFrameController.openDocument(arguments[1], arguments[2], arguments[3], "", arguments[4], "", arguments[5]);

				} else {
					treeData.unselectnode();
					if (typeof (arguments[5]) != "undefined") {
						top.weEditorFrameController.openDocument(arguments[1], arguments[2], arguments[3], "", arguments[4], "", arguments[5]);
					} else if (typeof (arguments[4]) != "undefined" && arguments[5] == "undefined") {
						top.weEditorFrameController.openDocument(arguments[1], arguments[2], arguments[3], "", "", "", arguments[5]);
					} else {
						top.weEditorFrameController.openDocument(arguments[1], arguments[2], arguments[3], "", arguments[4]);
					}
				}
				break;
			case "load":
				if (SEEMODE) {
					//	toggleBusy(1);
				} else {
					if (self.Tree) {
						if (self.Tree.setScrollY) {
							self.Tree.setScrollY();
						}
					}
					var tbl_prefix = '<?php echo TBL_PREFIX; ?>',
									table = (typeof arguments[1] != 'undefined' && arguments[1]) ? arguments[1] : 'tblFile';
					we_cmd("setTab", (tbl_prefix != '' && table.indexOf(tbl_prefix) !== 0 ? tbl_prefix + table : table));
					//toggleBusy(1);
					we_repl(self.load, url, arguments[0]);
				}
				break;
			case "exit_delete":
			case "exit_move":
				deleteMode = false;
				if (SEEMODE) {
				} else {
					treeData.setstate(treeData.tree_states["edit"]);
					drawTree();

					self.rframe.document.getElementById("bm_treeheaderDiv").style.height = "1px";
					self.rframe.document.getElementById("bm_mainDiv").style.top = "1px";
					top.setTreeWidth(widthBeforeDeleteMode);
					top.setSidebarWidth(widthBeforeDeleteModeSidebar);
				}
				break;
			case "delete":
				if (SEEMODE) {
					if (top.deleteMode != arguments[1]) {
						top.deleteMode = arguments[1];
					}
					if (arguments[2] != 1) {
						we_repl(top.weEditorFrameController.getActiveDocumentReference(), url, arguments[0]);
					}
				} else {
					if (top.deleteMode != arguments[1]) {
						top.deleteMode = arguments[1];
					}
					if (!top.deleteMode && treeData.state == treeData.tree_states["select"]) {
						treeData.setstate(treeData.tree_states["edit"]);
						drawTree();
					}
					self.rframe.document.getElementById("bm_treeheaderDiv").style.height = "150px";
					self.rframe.document.getElementById("bm_mainDiv").style.top = "150px";

					var width = top.getTreeWidth();

					widthBeforeDeleteMode = width;

					if (width < size.tree.deleteWidth) {
						top.setTreeWidth(size.tree.deleteWidth);
					}
					top.storeTreeWidth(widthBeforeDeleteMode);

					var widthSidebar = top.getSidebarWidth();

					widthBeforeDeleteModeSidebar = widthSidebar;

					if (arguments[2] != 1) {
						we_repl(self.rframe.treeheader, url, arguments[0]);
					}
				}
				break;
			case "move":
				if (SEEMODE) {
					if (top.deleteMode != arguments[1]) {
						top.deleteMode = arguments[1];
					}
					if (arguments[2] != 1) {
						we_repl(top.weEditorFrameController.getActiveDocumentReference(), url, arguments[0]);
					}
				} else {

					if (top.deleteMode != arguments[1]) {
						top.deleteMode = arguments[1];
					}
					if (!top.deleteMode && treeData.state == treeData.tree_states["selectitem"]) {
						treeData.setstate(treeData.tree_states["edit"]);
						drawTree();
					}
					self.rframe.document.getElementById("bm_treeheaderDiv").style.height = "160px";
					self.rframe.document.getElementById("bm_mainDiv").style.top = "160px";

					var width = top.getTreeWidth();

					widthBeforeDeleteMode = width;

					if (width < size.tree.moveWidth) {
						top.setTreeWidth(size.tree.moveWidth);
					}
					top.storeTreeWidth(widthBeforeDeleteMode);

					var widthSidebar = top.getSidebarWidth();

					widthBeforeDeleteModeSidebar = widthSidebar;

					if (arguments[2] != 1) {
						we_repl(self.rframe.treeheader, url, arguments[0]);
					}
				}
				break;

			default:
<?php
foreach(array_keys($jsCmd) as $mod){//fixme: if all commands have valid prefixes, we can do a switch/case instead of search
	echo 'if(we_cmd_' . $mod . '(arguments[0])){break;}';
}
?>
				if ((nextWindow = top.weEditorFrameController.getFreeWindow())) {
					_nextContent = nextWindow.getDocumentReference();
					we_repl(_nextContent, url, arguments[0]);
					// activate tab
					top.weMultiTabs.addTab(nextWindow.getFrameId(), ' &hellip; ', ' &hellip; ');
					// set Window Active and show it
					top.weEditorFrameController.setActiveEditorFrame(nextWindow.FrameId);
					top.weEditorFrameController.toggleFrames();
				} else {
					top.showMessage(g_l.no_editor_left, WE_MESSAGE_INFO, window);
				}
		}

	}

//-->
</script>
<?php
foreach($jsCmd as $cur){
	echo we_html_element::jsScript($cur);
}

echo we_html_element::jsScript(JS_DIR . 'webEdition.js');
$SEEM_edit_include = we_base_request::_(we_base_request::BOOL, "SEEM_edit_include");
we_main_header::pCSS($SEEM_edit_include);
?>
</head>
<body id="weMainBody" onbeforeunload="doUnload()">
	<?php
	flush();
//	get the frameset for the actual mode.
	pWebEdition_Frameset($SEEM_edit_include);
	we_main_header::pJS($SEEM_edit_include);
//	get the Treefunctions for docselector
	pWebEdition_Tree();
	?>
</body>
</html>
<?php
flush();
if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) && (!isset($SEEM_edit_include) || !$SEEM_edit_include)){
	session_write_close();
// trigger scheduler
	we_schedpro::trigger_schedule();
	// make the we_backup dir writable for all, so users can copy backupfiles with ftp in it
//	@chmod($_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR, 0777);
}