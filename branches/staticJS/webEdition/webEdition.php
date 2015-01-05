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
		'eplugin_exit_doc': "<?php echo g_l('alert', '[eplugin_exit_doc]'); ?>",
		"delete_single_confirm_delete": "<?php echo g_l('alert', '[delete_single][confirm_delete]'); ?>",
		'cockpit_reset_settings': '<?php echo g_l('alert', '[cockpit_reset_settings]'); ?>',
		'cockpit_not_activated': '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[cockpit_not_activated]'));?>',
		'no_perms': '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[no_perms]'));?>',
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
		'table_to_load': "<?php echo $_table_to_load; ?>",
		'TBL_PREFIX': '<?php echo TBL_PREFIX; ?>'
	};

	var dirs = {
		"WE_SHOP_MODULE_DIR": "<?php echo defined('WE_SHOP_MODULE_DIR') ? WE_SHOP_MODULE_DIR : ''; ?>",
		'WE_MODULES_DIR': "<?php echo WE_MODULES_DIR; ?>",
		'WE_MESSAGING_MODULE_DIR': "<?php echo defined('WE_MESSAGING_MODULE_DIR') ? WE_MESSAGING_MODULE_DIR : ''; ?>"
	};

	var SEEMODE =<?php echo intval($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE); ?>;
	var specialUnload =<?php echo intval(!(we_base_browserDetect::isChrome() || we_base_browserDetect::isSafari())); ?>;
	var docuLang="<?php echo ($GLOBALS["WE_LANGUAGE"] === 'Deutsch' ? 'de' : 'en'); ?>";

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

			case "open_template":
				we_cmd("load", tables.TEMPLATES_TABLE);
				url = "/webEdition/we_cmd.php?we_cmd[0]=openDocselector&we_cmd[8]=<?php echo we_base_ContentTypes::TEMPLATE; ?>&we_cmd[2]=" + tables.TEMPLATES_TABLE + "&we_cmd[5]="+encodeURIComponent("opener.top.weEditorFrameController.openDocument(table,currentID,currentType)")+"&we_cmd[9]=1";
				new jsWindow(url, "we_dirChooser", -1, -1, size.docSelect.width, size.docSelect.height, true, true, true, true);
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
			case "exit_doc_question":
				// return !! important for multiEditor
				return new jsWindow(url, "exit_doc_question", -1, -1, 380, 130, true, false, true);

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
			default:
<?php
$jsmods = array_keys($jsCmd);
$jsmods[] = 'base';
foreach($jsmods as $mod){//fixme: if all commands have valid prefixes, we can do a switch/case instead of search
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