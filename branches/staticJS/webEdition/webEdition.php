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

if(!isset($_SESSION['weS']['we_mode'])){
	$_SESSION['weS']['we_mode'] = we_base_constants::MODE_NORMAL;
}
//	check session
we_html_tools::protect(null, WEBEDITION_DIR . 'index.php');

we_base_file::cleanTempFiles();

/**
 * @return void
 * @desc prints the functions needed for the tree.
 */
function pWebEdition_Tree(){
	switch($_SESSION['weS']['we_mode']){
		default:
		case we_base_constants::MODE_NORMAL:
			$Tree = new weMainTree("webEdition.php", "top", "self.Tree", "top.load");
			echo $Tree->getJSTreeCode();
			break;
		case we_base_constants::MODE_SEE:
			echo we_html_element::jsElement('
function makeNewEntry(icon,id,pid,txt,open,typ,tab){
}
function drawTree(){
}

function info(text){
}');
			break;
	}
}

/**
 * @return void
 * @desc the frameset for the SeeMode
 */
function pWebEdition_Frameset($SEEM_edit_include){
	switch($_SESSION['weS']['we_mode']){
		default:
		case we_base_constants::MODE_NORMAL:
			$we_cmds = '';
			break;
		case we_base_constants::MODE_SEE:
			$we_cmds = '';
			if(isset($GLOBALS["SEEM_edit_include"]) && $GLOBALS["SEEM_edit_include"]){ // edit include file
				$_REQUEST["SEEM_edit_include"] = true;
				$we_cmds = "?we_cmd[0]=edit_document&";

				for($i = 1; $i < count($_REQUEST['we_cmd']); $i++){
					$we_cmds .= "we_cmd[" . $i . "]=" . $_REQUEST['we_cmd'][$i] . "&";
				}
				$we_cmds.='&SEEM_edit_include=true';
			}
			break;
	}
	?>
	<div style="position:absolute;top:0px;left:0px;right:0px;height:32px;border-bottom: 1px solid black;">
		<?php we_main_header::pbody($SEEM_edit_include); ?>
	</div>
	<div style="position:absolute;top:32px;left:0px;right:0px;bottom:0px;border: 0px;">
		<iframe src="<?php echo WEBEDITION_DIR; ?>resizeframe.php?<?php echo $we_cmds ? '?' . $we_cmds : ''; ?>" id="rframe" name="rframe"></iframe>
	</div>
	<div style="position:absolute;left:0px;right:0px;bottom:0px;height: 0px;">
		<iframe src="about:blank" style="overflow: hidden;" name="load"></iframe>
		<iframe src="about:blank" style="overflow: hidden;" name="load2"></iframe>
		<iframe src="about:blank" style="border-right:1px solid black;width:100%;height:100%;overflow: hidden;" name="ad"></iframe>
		<iframe src="about:blank" style="border-right:1px solid black;width:100%;height:100%;overflow: hidden;" name="postframe"></iframe>
		<iframe src="about:blank" style="border-right:1px solid black;width:100%;height:100%;overflow: hidden;" name="plugin"></iframe>
		<?php include(WE_USERS_MODULE_PATH . 'we_users_ping.inc.php'); ?>
	</div>
	<?php
}

/* $sn = SERVER_NAME;

  if(strstr($sn, '@')) {
  list($foo,$sn) = explode('@',$sn);
  }
 */
//	unlock everything old, when a new window is opened.
if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) != "edit_include_document"){
	$GLOBALS['DB_WE']->query('DELETE FROM ' . LOCK_TABLE . ' WHERE lockTime<NOW()');
}
$GLOBALS['DB_WE']->query('UPDATE ' . USER_TABLE . '	SET Ping=0 WHERE Ping<UNIX_TIMESTAMP(NOW()-' . (we_base_constants::PING_TIME + we_base_constants::PING_TOLERANZ) . ')');


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

echo we_html_tools::getHtmlTop('webEdition - ' . $_SESSION['user']['Username']) .
 STYLESHEET;
?>
<script type="text/javascript"><!--
<?php
echo we_tool_lookup::getJsCmdInclude($jsCmd) .
 we_message_reporting::jsString();
?>

if (self.location !== top.location) {
	top.location = self.location;
}

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
var SEEMODE =<?php echo intval($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE); ?>;
var seeMode_edit_include = <?php echo (isset($SEEM_edit_include) && $SEEM_edit_include) ? "true" : "false"; ?>; // in edit_include mode of seeMode
var userID =<?php echo $_SESSION["user"]["ID"]; ?>;
var sess_id = "<?php echo session_id(); ?>";
var specialUnload =<?php echo intval(!(we_base_browserDetect::isChrome() || we_base_browserDetect::isSafari())); ?>;
var docuLang = "<?php echo ($GLOBALS["WE_LANGUAGE"] === 'Deutsch' ? 'de' : 'en'); ?>";
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
	'cockpit_not_activated': '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[cockpit_not_activated]')); ?>',
	'no_perms': '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[no_perms]')); ?>',
	'nav_first_document': '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[navigation][first_document]')); ?>',
	'nav_last_document': '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[navigation][last_document]')); ?>',
	'nav_no_open_document': '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[navigation][no_open_document]')); ?>',
	'nav_no_entry': '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[navigation][no_entry]')); ?>'
};
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
	'sidebar': {
		'defaultWidth':<?php echo SIDEBAR_DEFAULT_WIDTH; ?>
	}
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
	'WE_MESSAGING_MODULE_DIR': "<?php echo defined('WE_MESSAGING_MODULE_DIR') ? WE_MESSAGING_MODULE_DIR : ''; ?>",
	'BUTTONS_DIR': "<?php echo BUTTONS_DIR; ?>"
};
var contentTypes = {
	'TEMPLATE': '<?php echo we_base_ContentTypes::TEMPLATE; ?>',
	'WEDOCUMENT': '<?php echo we_base_ContentTypes::WEDOCUMENT; ?>',
	'OBJECT_FILE': '<?php echo we_base_ContentTypes::OBJECT_FILE; ?>',
};
var WE_EDITPAGE_CONTENT =<?php echo we_base_constants::WE_EDITPAGE_CONTENT; ?>;
/*##################### messaging function #####################*/

// this variable contains settings how to deal with settings
// it has to be set when changing the preferences
/**
 * setting integer, any sum of 1,2,4
 */
var messageSettings = <?php echo (isset($_SESSION["prefs"]["message_reporting"]) && $_SESSION["prefs"]["message_reporting"] > 0 ? we_message_reporting::WE_MESSAGE_ERROR | $_SESSION["prefs"]["message_reporting"] : (we_message_reporting::WE_MESSAGE_ERROR | we_message_reporting::WE_MESSAGE_WARNING | we_message_reporting::WE_MESSAGE_NOTICE)); ?>;
var weEditorWasLoaded = false;
var setPageNrCallback = {
	success: function (o) {
	},
	failure: function (o) {
		alert(g_l.unable_to_call_setpagenr);
	}
};
//-->
</script>
<?php
echo
we_html_element::jsScript(JS_DIR . 'windows.js') .
 we_html_element::jsScript(JS_DIR . 'weTinyMceDialogs.js') .
 we_html_element::jsScript(JS_DIR . 'weNavigationHistory.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/yahoo-min.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/event-min.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/connection-min.js') .
 we_html_element::jsScript(JS_DIR . 'keyListener.js') .
 we_html_element::jsScript(JS_DIR . 'messageConsole.js') .
 we_html_element::jsScript(JS_DIR . 'we_showMessage.js') .
 we_html_element::jsScript(JS_DIR . 'webEdition.js') .
 we_html_element::jsScript(JS_DIR . 'weSidebar.js');


foreach($jsCmd as $cur){
	echo we_html_element::jsScript($cur);
}
?>
<script type="text/javascript"><!--
top.weSidebar = weSidebar;
	function we_cmd() {
		var url = "/webEdition/we_cmd.php?";
		for (var i = 0; i < arguments.length; i++) {
			url += "we_cmd[" + i + "]=" + encodeURIComponent(arguments[i]);
			if (i < (arguments.length - 1))
				url += "&";
		}

		/*if (window.screen) {
		 h = ((screen.height - 100) > screen.availHeight) ? screen.height - 100 : screen.availHeight;
		 w = screen.availWidth;
		 }*/

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
// deal with not activated modules
$diff = array_diff(array_keys(we_base_moduleInfo::getAllModules()), $GLOBALS['_we_active_integrated_modules']);
if($diff){
	foreach($diff as $m){
		echo 'case "' . $m . '_edit_ifthere":
';
	}
	echo 'new jsWindow(url,"module_info",-1,-1,380,250,true,true,true);
		break;';
}
?>
			case "exit_doc_question":
				// return !! important for multiEditor
				return new jsWindow(url, "exit_doc_question", -1, -1, 380, 130, true, false, true);
			case "eplugin_exit_doc" :
				if (top.plugin !== undefined && top.plugin.document.WePlugin !== undefined) {
					if (top.plugin.isInEditor(arguments[1])) {
						return confirm(g_l.eplugin_exit_doc);
					}
				}
				return true;
			case "editor_plugin_doc_count":
				if (top.plugin.document.WePlugin !== undefined) {
					return top.plugin.getDocCount();
				}
				return 0;
			default:
<?php
$jsmods = array_keys($jsCmd);
$jsmods[] = 'base';
$jsmods[] = 'tools';
foreach($jsmods as $mod){//fixme: if all commands have valid prefixes, we can do a switch/case instead of search
	echo 'if(we_cmd_' . $mod . '(arguments,url)){break;}';
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
if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) && (!isset($SEEM_edit_include) || !$SEEM_edit_include)){
	flush();
	if(function_exists('fastcgi_finish_request')){
		fastcgi_finish_request();
	}
	session_write_close();
// trigger scheduler
	we_schedpro::trigger_schedule();
}
