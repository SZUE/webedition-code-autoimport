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
var treeData={
	makeNewEntry:function(){
	}
}
function drawTree(){
}

function info(text){
}');
			break;
	}
}

//	Here begins the code for showing the correct frameset.
//	To improve readability the different cases are outsourced
//	in several functions, for SEEM, normal or edit_include-Mode.

function getSidebarWidth(){
// Get the width of the sidebar
	if(SIDEBAR_DISABLED != 1 && SIDEBAR_SHOW_ON_STARTUP == 1){
		return SIDEBAR_DEFAULT_WIDTH;
	}
	return 0;
}

//set/update locks & active documents
we_users_user::updateActiveUser();

/* $sn = SERVER_NAME;

  if(strstr($sn, '@')) {
  list($foo,$sn) = explode('@',$sn);
  }
 */
//	unlock everything old, when a new window is opened.
if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) != "edit_include_document"){
	$GLOBALS['DB_WE']->query('DELETE FROM ' . LOCK_TABLE . ' WHERE lockTime<NOW()');
}
$GLOBALS['DB_WE']->query('UPDATE ' . USER_TABLE . '	SET Ping=NULL WHERE Ping<(NOW()-INTERVAL ' . (we_base_constants::PING_TIME + we_base_constants::PING_TOLERANZ) . ' second)');


if(permissionhandler::hasPerm("CAN_SEE_DOCUMENTS")){
	$_table_to_load = FILE_TABLE;
} else if(permissionhandler::hasPerm("CAN_SEE_TEMPLATES")){
	$_table_to_load = TEMPLATES_TABLE;
} else if(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTFILES")){
	$_table_to_load = OBJECT_FILES_TABLE;
} else if(defined('OBJECT_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTS")){
	$_table_to_load = OBJECT_TABLE;
} else if(permissionhandler::hasPerm("CAN_SEE_COLLECTIONS")){
	$_table_to_load = VFILE_TABLE;
} else {
	$_table_to_load = "";
}

$jsCmd = array();
foreach($GLOBALS['_we_active_integrated_modules'] as $mod){
	if(file_exists(WE_JS_MODULES_PATH . $mod . '/we_webEditionCmd_' . $mod . '.js')){
		$jsCmd[$mod] = WE_JS_MODULES_DIR . $mod . '/we_webEditionCmd_' . $mod . '.js';
	}
}

echo we_html_tools::getHtmlTop('webEdition - ' . $_SESSION['user']['Username'],'','', '', '', false) .
 STYLESHEET;
?>
<script><!--
<?php
echo we_tool_lookup::getJsCmdInclude($jsCmd);
$jsmods = array_keys($jsCmd);
$jsmods[] = 'base';
$jsmods[] = 'tools';

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
var hot = 0;
var last = 0;
var lastUsedLoadFrame = null;
var nlHTMLMail = 0;
var browserwind = null;
var makefocus = null;
var weplugin_wait = null;
// is set in headermenu.php
//var weSidebar = null;
// seeMode
var specialUnload =<?php echo intval(!(we_base_browserDetect::isChrome() || we_base_browserDetect::isSafari())); ?>;

// TODO: move to some JS-file
var dd = {
		dataTransfer: {
			text : ''
		}
	};

var modules = {
	MESSAGING_SYSTEM:<?php echo intval(defined('MESSAGING_SYSTEM')); ?>
};

/*##################### messaging function #####################*/

var setPageNrCallback = {
	success: function (o) {
	},
	failure: function (o) {
		alert(WE().consts.g_l.main.unable_to_call_setpagenr);
	}
};

var WebEdition={
	//all constants in WE used in JS
		consts:{
			contentTypes:{
				TEMPLATE: '<?php echo we_base_ContentTypes::TEMPLATE; ?>',
				WEDOCUMENT: '<?php echo we_base_ContentTypes::WEDOCUMENT; ?>',
				OBJECT_FILE: '<?php echo we_base_ContentTypes::OBJECT_FILE; ?>',
				IMAGE: "<?php echo we_base_ContentTypes::IMAGE; ?>",
				HTML: "<?php echo we_base_ContentTypes::HTML; ?>",
				FLASH: "<?php echo we_base_ContentTypes::FLASH; ?>",
				QUICKTIME: "<?php echo we_base_ContentTypes::QUICKTIME; ?>",
				VIDEO: "<?php echo we_base_ContentTypes::VIDEO; ?>",
				AUDIO: "<?php echo we_base_ContentTypes::AUDIO; ?>",
				JS: "<?php echo we_base_ContentTypes::JS; ?>",
				TEXT: "<?php echo we_base_ContentTypes::TEXT; ?>",
				XML: "<?php echo we_base_ContentTypes::XML; ?>",
				HTACESS: "<?php echo we_base_ContentTypes::HTACESS; ?>",
				CSS: "<?php echo we_base_ContentTypes::CSS; ?>",
				APPLICATION: "<?php echo we_base_ContentTypes::APPLICATION; ?>",
				COLLECTION: "<?php echo we_base_ContentTypes::COLLECTION; ?>"
			},
			dirs:{
				WEBEDITION_DIR:"<?php echo WEBEDITION_DIR; ?>",
				WE_SHOP_MODULE_DIR: "<?php echo defined('WE_SHOP_MODULE_DIR') ? WE_SHOP_MODULE_DIR : ''; ?>",
				WE_MODULES_DIR: "<?php echo WE_MODULES_DIR; ?>",
				WE_MESSAGING_MODULE_DIR: "<?php echo defined('WE_MESSAGING_MODULE_DIR') ? WE_MESSAGING_MODULE_DIR : ''; ?>",
				IMAGE_DIR:"<?php echo IMAGE_DIR; ?>",
				ICON_DIR:"<?php echo ICON_DIR; ?>",
				WE_CUSTOMER_MODULE_DIR:"<?php echo defined('WE_CUSTOMER_MODULE_DIR') ? WE_CUSTOMER_MODULE_DIR : 'WE_CUSTOMER_MODULE_DIR'; ?>",
				WE_INCLUDES_DIR:"<?php echo WE_INCLUDES_DIR; ?>",
				WE_SHOP_MODULE_DIR: "<?php echo defined('WE_SHOP_MODULE_DIR') ? WE_SHOP_MODULE_DIR : 'WE_SHOP_MODULE_DIR'; ?>",
				WE_WORKFLOW_MODULE_DIR: "<?php echo defined('WE_WORKFLOW_MODULE_DIR') ? WE_WORKFLOW_MODULE_DIR : 'WE_WORKFLOW_MODULE_DIR'; ?>",
			},
			g_l:{
				main:{
					unable_to_call_setpagenr: '<?php echo g_l('global', '[unable_to_call_setpagenr]'); ?>',
					open_link_in_SEEM_edit_include: '<?php echo we_message_reporting::prepareMsgForJS(g_l('SEEM', '[open_link_in_SEEM_edit_include]')); ?>',
					browser_crashed: '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[browser_crashed]')); ?>',
					no_perms_action: '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[no_perms_action]')); ?>',
					no_document_opened: '<?php echo we_message_reporting::prepareMsgForJS(g_l('global', '[no_document_opened]')); ?>',
					no_editor_left: "<?php echo g_l('multiEditor', '[no_editor_left]'); ?>",
					eplugin_exit_doc: "<?php echo g_l('alert', '[eplugin_exit_doc]'); ?>",
					delete_single_confirm_delete: "<?php echo g_l('alert', '[delete_single][confirm_delete]'); ?>",
					cockpit_reset_settings: '<?php echo g_l('alert', '[cockpit_reset_settings]'); ?>',
					cockpit_not_activated: '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[cockpit_not_activated]')); ?>',
					no_perms: '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[no_perms]')); ?>',
					nav_first_document: '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[navigation][first_document]')); ?>',
					nav_last_document: '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[navigation][last_document]')); ?>',
					nav_no_open_document: '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[navigation][no_open_document]')); ?>',
					nav_no_entry: '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[navigation][no_entry]')); ?>',
					unable_to_call_ping: '<?php echo g_l('global', '[unable_to_call_ping]'); ?>',
					nothing_to_save: "<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[nothing_to_save]')) ?>",
					nothing_to_publish: "<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[nothing_to_publish]')) ?>",
					nothing_to_delete: "<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[nothing_to_delete]')) ?>",
					save_error_fields_value_not_valid: "<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[save_error_fields_value_not_valid]')); ?>",
					name_nok:"<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[name_nok]')); ?>",
					prefs_saved_successfully: "<?php echo we_message_reporting::prepareMsgForJS(g_l('cockpit', '[prefs_saved_successfully]')); ?>",
					copy_folder_not_valid: "<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[copy_folder_not_valid]')); ?>",
					folder_copy_success: "<?php echo we_message_reporting::prepareMsgForJS(g_l('copyFolder', '[copy_success]')); ?>",
				},
				message_reporting:{
					notice:"<?php echo g_l('alert', '[notice]');?>",
					warning:"<?php echo g_l('alert', '[warning]');?>",
					error:"<?php echo g_l('alert', '[error]');?>",
					msgNotice:"<?php echo g_l('messageConsole', '[iconBar][notice]');?>",
					msgWarning:"<?php echo g_l('messageConsole', '[iconBar][warning]');?>",
					msgError:"<?php echo g_l('messageConsole', '[iconBar][error]');?>",
				},
				cockpit:{
				},
				<?php
				foreach($jsmods as $mod){
					echo $mod.':{},';
				}
				?>
			},
			global:{
				WE_EDITPAGE_CONTENT:<?php echo we_base_constants::WE_EDITPAGE_CONTENT; ?>,
				PING_TIME:<?php echo we_base_constants::PING_TIME * 1000; ?>
			},
			message:{
				WE_MESSAGE_INFO: <?php echo we_message_reporting::WE_MESSAGE_INFO; ?>,
				WE_MESSAGE_FRONTEND: <?php echo we_message_reporting::WE_MESSAGE_FRONTEND; ?>,
				WE_MESSAGE_NOTICE:<?php echo we_message_reporting::WE_MESSAGE_NOTICE; ?>,
				WE_MESSAGE_WARNING:<?php echo we_message_reporting::WE_MESSAGE_WARNING; ?>,
				WE_MESSAGE_ERROR:<?php echo we_message_reporting::WE_MESSAGE_ERROR; ?>,
			},
			tables: {
				OBJECT_FILES_TABLE: "<?php echo defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'; ?>",
				OBJECT_TABLE: "<?php echo defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'; ?>",
				TBL_PREFIX: '<?php echo TBL_PREFIX; ?>',
<?php
foreach(we_base_request::getAllTables() as $k => $v){
	echo $k . ':"' . $v . '",';
}
?>
			},
			size:{
				tree: {
					hidden:<?php echo weTree::HiddenWidth; ?>,
					defaultWidth:<?php echo weTree::DefaultWidth; ?>,
					min:<?php echo weTree::MinWidth; ?>,
					max:<?php echo weTree::MaxWidth; ?>,
					step:<?php echo weTree::StepWidth; ?>,
					moveWidth:<?php echo weTree::MoveWidth; ?>,
					deleteWidth:<?php echo weTree::DeleteWidth; ?>
				},
				catSelect: {
					width:<?php echo we_selector_file::WINDOW_CATSELECTOR_WIDTH; ?>,
					height:<?php echo we_selector_file::WINDOW_CATSELECTOR_HEIGHT; ?>
				},
				docSelect: {
					width:<?php echo we_selector_file::WINDOW_DOCSELECTOR_WIDTH; ?>,
					height:<?php echo we_selector_file::WINDOW_DOCSELECTOR_HEIGHT; ?>
				},
				windowSelect: {
					width:<?php echo we_selector_file::WINDOW_SELECTOR_WIDTH; ?>,
					height:<?php echo we_selector_file::WINDOW_SELECTOR_HEIGHT; ?>
				},
				windowDirSelect: {
					width:<?php echo we_selector_file::WINDOW_DIRSELECTOR_WIDTH; ?>,
					height:<?php echo we_selector_file::WINDOW_DIRSELECTOR_HEIGHT; ?>
				},
				windowDelSelect: {
					width:<?php echo we_selector_file::WINDOW_DELSELECTOR_WIDTH; ?>,
					height:<?php echo we_selector_file::WINDOW_DELSELECTOR_HEIGHT; ?>
				},
				sidebar: {
					defaultWidth:<?php echo SIDEBAR_DEFAULT_WIDTH; ?>
				}
			},
			linkPrefix: {
				TYPE_OBJ_PREFIX: '<?php echo we_base_link::TYPE_OBJ_PREFIX; ?>',
				TYPE_INT_PREFIX: '<?php echo we_base_link::TYPE_INT_PREFIX; ?>',
				TYPE_MAIL_PREFIX: '<?php echo we_base_link::TYPE_MAIL_PREFIX; ?>'
			},
		},

	//all relevant settings for current session
	session:{
		seemode:<?php echo intval($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE); ?>,
		seeMode_edit_include:<?php echo (!empty($SEEM_edit_include)) ? 'true' : 'false'; ?>, // in edit_include mode of seeMode
		userID:<?php echo $_SESSION["user"]["ID"]; ?>,
		//permissions set for the user
		permissions:{
		<?php
		foreach($_SESSION['perms'] as $perm => $access){
			echo  $perm . ':' . ($_SESSION['perms']['ADMINISTRATOR'] ? 1 : intval($access)) . ',';
		}
		?>
		},
		sess_id:"<?php echo session_id(); ?>",
		specialUnload:specialUnload,
		docuLang:"<?php echo ($GLOBALS["WE_LANGUAGE"] === 'Deutsch' ? 'de' : 'en'); ?>",
		helpLang:"<?php echo $GLOBALS["WE_LANGUAGE"]; ?>",
		messageSettings:<?php echo (!empty($_SESSION['prefs']['message_reporting']) ? we_message_reporting::WE_MESSAGE_ERROR | $_SESSION['prefs']['message_reporting'] : (we_message_reporting::WE_MESSAGE_ERROR | we_message_reporting::WE_MESSAGE_WARNING | we_message_reporting::WE_MESSAGE_NOTICE)); ?>,
		isChrome:<?php echo intval(we_base_browserDetect::isChrome()); ?>,
	},
	layout:{
		//vtabs:Vtabs,
		button:null,
		sidebar:null,
		cockpitFrame:null,
		windows:[],
	},
	handler:{
		errorHandler:errorHandler,
		dealWithKeyboardShortCut:null,
	},
	//utility functions, defined in webedition.js
	util:{
	}
};
//-->
</script>
<?php
echo we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'weTinyMceDialogs.js') .
 we_html_element::jsScript(JS_DIR . 'weNavigationHistory.js') .
 we_html_element::jsScript(LIB_DIR . 'additional/yui/yahoo-min.js') .
 we_html_element::jsScript(LIB_DIR . 'additional/yui/event-min.js') .
 we_html_element::jsScript(LIB_DIR . 'additional/yui/connection-min.js') .
 we_html_element::jsScript(JS_DIR . 'keyListener.js', 'WE().handler.dealWithKeyboardShortCut = dealWithKeyboardShortCut;') .
 we_html_element::jsScript(JS_DIR . 'windows.js', 'WE().util.jsWindow = jsWindow;WE().util.jsWindow;').
		we_html_element::jsScript(JS_DIR . 'we_tabs/we_tabs.js') .
 we_html_element::jsScript(JS_DIR . 'messageConsole.js') .
 we_html_element::jsScript(JS_DIR . 'webEdition.js') .
 we_html_element::jsScript(JS_DIR . 'weSidebar.js') .
 we_html_element::jsScript(JS_DIR . 'weButton.js') .
 we_html_element::jsScript(JS_DIR . 'we_users_ping.js') .
 we_main_headermenu::css();

foreach($jsCmd as $cur){
	echo we_html_element::jsScript($cur);
}
?>
<script><!--
	function we_cmd() {
	var url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
	if(typeof arguments[0] === 'object' && arguments[0]['we_cmd[0]'] !== undefined){
		var args = {}, i = 0, tmp = arguments[0];
		scope = this;
		url += Object.keys(tmp).map(function(key){args[key] = tmp[key]; args[i++] = tmp[key]; return key + "=" + encodeURIComponent(tmp[key]);}).join("&");
	} else {
		var args = Array.prototype.slice.call(arguments);
		for (i=0; i < args.length; i++) {
			url += "we_cmd[" + i + "]=" + encodeURIComponent(args[i])+(i < (args.length - 1)?"&":"");
		}
	}

	//	When coming from a we_cmd, always mark the document as opened with we !!!!
	if (WE().layout.weEditorFrameController.getActiveDocumentReference) {

		switch(args[0]){
			case 'edit_document':
			case 'new_document':
			case 'open_extern_document':
			case 'edit_document_with_parameters':
			case 'new_folder':
			case 'edit_folder':
				break;
			default:
				WE().layout.weEditorFrameController.getActiveDocumentReference().openedWithWE = true;
		}

	}
	switch (args[0]) {
<?php
// deal with not activated modules
$diff = array_diff(array_keys(we_base_moduleInfo::getAllModules()), $GLOBALS['_we_active_integrated_modules']);
if($diff){
	foreach($diff as $m){
		echo 'case "' . $m . '_edit_ifthere":
';
	}
	echo 'new (WE().util.jsWindow)(window, url,"module_info",-1,-1,380,250,true,true,true);
		break;';
}
?>
	case "exit_doc_question":
					// return !! important for multiEditor
					return new (WE().util.jsWindow)(window, url, "exit_doc_question", - 1, - 1, 380, 130, true, false, true);
	case "eplugin_exit_doc" :
		if (top.plugin !== undefined && top.plugin.document.WePlugin !== undefined) {
			if (top.plugin.isInEditor(args[1])) {
				return confirm(WE().consts.g_l.main.eplugin_exit_doc);
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
foreach($jsmods as $mod){//fixme: if all commands have valid prefixes, we can do a switch/case instead of search
	echo 'if(we_cmd_' . $mod . '.apply(this,[args, url])){
	break;
}';
}
?>
		we_showInNewTab(args,url);
		}

	}
//-->
</script>
</head>
<body id="weMainBody" onload="initWE();top.start('<?php echo $_table_to_load;?>');" onbeforeunload="doUnload()">
	<div id="headerDiv"><?php
	$SEEM_edit_include = we_base_request::_(we_base_request::BOOL, 'SEEM_edit_include');
		we_main_header::pbody($SEEM_edit_include); ?>
	</div>
	<div id="resizeFrame"><?php
		$_sidebarwidth = getSidebarWidth();
		switch($_SESSION['weS']['we_mode']){
			default:
			case we_base_constants::MODE_NORMAL:
				$treewidth = isset($_COOKIE["treewidth_main"]) && ($_COOKIE["treewidth_main"] >= weTree::MinWidth) ? intval($_COOKIE["treewidth_main"]) : weTree::DefaultWidth;
				$treeStyle = 'display:block;';
				break;
			case we_base_constants::MODE_SEE:
				$treewidth = 0;
				if($SEEM_edit_include){ // edit include file
					$_REQUEST['SEEM_edit_include'] = true;
					$_REQUEST['we_cmd'][0] = 'edit_document';
				}
				$treeStyle = '';
				break;
		}
		?>
		<div style="width:<?php echo $treewidth; ?>px;<?php echo $treeStyle; ?>" id="bframeDiv">
			<?php include(WE_INCLUDES_PATH . 'baumFrame.inc.php'); ?>
		</div>
		<div style="position:absolute;top:0px;bottom:0px;right:<?php echo $_sidebarwidth; ?>px;left:<?php echo $treewidth; ?>px;border-left:1px solid black;overflow: hidden;" id="bm_content_frameDiv">
			<iframe frameBorder="0" src="<?php echo WEBEDITION_DIR; ?>multiContentFrame.php" name="bm_content_frame" style="border:0px;width:100%;height:100%;overflow: hidden;"></iframe>
		</div>
		<?php
		if(!(SIDEBAR_DISABLED == 1)){
			?>
			<div style="position:absolute;top:0px;bottom:0px;right:0px;width:<?php echo $_sidebarwidth; ?>px;border-left:1px solid black;" id="sidebarDiv">
				<?php
				$weFrame = new we_sidebar_frames();
				$weFrame->getHTML('');
				?>
			</div>
		<?php } ?>
	</div>
	<div id="cmdDiv">
		<iframe src="about:blank" name="load"></iframe>
		<iframe src="about:blank" name="load2"></iframe>
		<!-- iframe src="about:blank" name="postframe"></iframe -->
		<iframe src="about:blank" name="plugin"></iframe>
	</div>
	<?php
//	get the frameset for the actual mode.
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