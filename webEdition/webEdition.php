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
function getWebEdition_Tree(){
	switch($_SESSION['weS']['we_mode']){
		default:
		case we_base_constants::MODE_NORMAL:
			$Tree = new we_tree_main("webEdition.php", "top", "top", "top.load");
			return $Tree->getJSTreeCode();
		case we_base_constants::MODE_SEE:
			return we_html_element::jsElement('
var treeData={
	makeNewEntry:function(){
	},
	unselectNode:function(){
	}
}
function drawTree(){
}

function info(text){
}');
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
	$table_to_load = FILE_TABLE;
} else if(permissionhandler::hasPerm("CAN_SEE_TEMPLATES")){
	$table_to_load = TEMPLATES_TABLE;
} else if(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTFILES")){
	$table_to_load = OBJECT_FILES_TABLE;
} else if(defined('OBJECT_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTS")){
	$table_to_load = OBJECT_TABLE;
} else if(permissionhandler::hasPerm("CAN_SEE_COLLECTIONS")){
	$table_to_load = VFILE_TABLE;
} else {
	$table_to_load = "";
}

$jsCmd = [];
foreach($GLOBALS['_we_active_integrated_modules'] as $mod){
	if(file_exists(WE_JS_MODULES_PATH . $mod . '/we_webEditionCmd_' . $mod . '.js')){
		$jsCmd[$mod] = WE_JS_MODULES_DIR . $mod . '/we_webEditionCmd_' . $mod . '.js';
	}
}

echo we_html_tools::getHtmlTop('webEdition - ' . $_SESSION['user']['Username'], '', '', '', '', false);
?>
<script><!--
<?php
echo we_tool_lookup::getJsCmdInclude($jsCmd);
$jsmods = array_keys($jsCmd);
$jsmods[] = 'base';
$jsmods[] = 'tools';

if(!empty($_SESSION['WE_USER_PASSWORD_NOT_SUFFICIENT'])){
	echo 'alert("' . g_l('global', '[pwd][startupRegExFailed]') . '");';
	unset($_SESSION['WE_USER_PASSWORD_NOT_SUFFICIENT']);
}
?>

if (self.location !== top.location) {
top.location = self.location;
}

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
var weplugin_wait = null;
// is set in headermenu.php
//var weSidebar = null;
// seeMode
var specialUnload =<?= intval(!(we_base_browserDetect::isChrome() || we_base_browserDetect::isSafari())); ?>;
// TODO: move to some JS-file
var dd = {
dataTransfer: {
text : ''
				}
};
var WebEdition = {
	//all constants in WE used in JS
	consts:{
		contentTypes:{
			TEMPLATE: '<?= we_base_ContentTypes::TEMPLATE; ?>',
			WEDOCUMENT: '<?= we_base_ContentTypes::WEDOCUMENT; ?>',
			OBJECT_FILE: '<?= we_base_ContentTypes::OBJECT_FILE; ?>',
			IMAGE: "<?= we_base_ContentTypes::IMAGE; ?>",
			HTML: "<?= we_base_ContentTypes::HTML; ?>",
			FLASH: "<?= we_base_ContentTypes::FLASH; ?>",
			VIDEO: "<?= we_base_ContentTypes::VIDEO; ?>",
			AUDIO: "<?= we_base_ContentTypes::AUDIO; ?>",
			JS: "<?= we_base_ContentTypes::JS; ?>",
			TEXT: "<?= we_base_ContentTypes::TEXT; ?>",
			XML: "<?= we_base_ContentTypes::XML; ?>",
			HTACCESS: "<?= we_base_ContentTypes::HTACCESS; ?>",
			CSS: "<?= we_base_ContentTypes::CSS; ?>",
			APPLICATION: "<?= we_base_ContentTypes::APPLICATION; ?>",
			COLLECTION: "<?= we_base_ContentTypes::COLLECTION; ?>"
		},
		dirs:{
			WEBEDITION_DIR:"<?= WEBEDITION_DIR; ?>",
			WE_MODULES_DIR: "<?= WE_MODULES_DIR; ?>",
			WE_SHOP_MODULE_DIR: "<?= defined('WE_SHOP_MODULE_DIR') ? WE_SHOP_MODULE_DIR : ''; ?>",
			WE_MESSAGING_MODULE_DIR: "<?= defined('WE_MESSAGING_MODULE_DIR') ? WE_MESSAGING_MODULE_DIR : ''; ?>",
			WE_CUSTOMER_MODULE_DIR:"<?= defined('WE_CUSTOMER_MODULE_DIR') ? WE_CUSTOMER_MODULE_DIR : 'WE_CUSTOMER_MODULE_DIR'; ?>",
			WE_INCLUDES_DIR:"<?= WE_INCLUDES_DIR; ?>",
			WE_SHOP_MODULE_DIR: "<?= defined('WE_SHOP_MODULE_DIR') ? WE_SHOP_MODULE_DIR : 'WE_SHOP_MODULE_DIR'; ?>",
			WE_WORKFLOW_MODULE_DIR: "<?= defined('WE_WORKFLOW_MODULE_DIR') ? WE_WORKFLOW_MODULE_DIR : 'WE_WORKFLOW_MODULE_DIR'; ?>",
			WE_JS_TINYMCE_DIR: "<?= WE_JS_TINYMCE_DIR; ?>",
			WE_SPELLCHECKER_MODULE_DIR:"<?= defined('SPELLCHECKER') ? WE_SPELLCHECKER_MODULE_DIR : ''; ?>",
		},
		g_l:{
			main:{
				unable_to_call_setpagenr: '<?= g_l('global', '[unable_to_call_setpagenr]'); ?>',
				open_link_in_SEEM_edit_include: '<?= we_message_reporting::prepareMsgForJS(g_l('SEEM', '[open_link_in_SEEM_edit_include]')); ?>',
				no_perms_action: '<?= we_message_reporting::prepareMsgForJS(g_l('alert', '[no_perms_action]')); ?>',
				no_document_opened: '<?= we_message_reporting::prepareMsgForJS(g_l('global', '[no_document_opened]')); ?>',
				no_editor_left: "<?= g_l('multiEditor', '[no_editor_left]'); ?>",
				eplugin_exit_doc: "<?= g_l('alert', '[eplugin_exit_doc]'); ?>",
				delete_single_confirm_delete: "<?= g_l('alert', '[delete_single][confirm_delete]'); ?>\n",
				no_perms: '<?= we_message_reporting::prepareMsgForJS(g_l('alert', '[no_perms]')); ?>',
				nav_first_document: '<?= we_message_reporting::prepareMsgForJS(g_l('alert', '[navigation][first_document]')); ?>',
				nav_last_document: '<?= we_message_reporting::prepareMsgForJS(g_l('alert', '[navigation][last_document]')); ?>',
				nav_no_open_document: '<?= we_message_reporting::prepareMsgForJS(g_l('alert', '[navigation][no_open_document]')); ?>',
				nav_no_entry: '<?= we_message_reporting::prepareMsgForJS(g_l('alert', '[navigation][no_entry]')); ?>',
				unable_to_call_ping: '<?= g_l('global', '[unable_to_call_ping]'); ?>',
				nothing_to_save: "<?= we_message_reporting::prepareMsgForJS(g_l('alert', '[nothing_to_save]')) ?>",
				nothing_to_publish: "<?= we_message_reporting::prepareMsgForJS(g_l('alert', '[nothing_to_publish]')) ?>",
				nothing_to_delete: "<?= we_message_reporting::prepareMsgForJS(g_l('alert', '[nothing_to_delete]')) ?>",
				nothing_to_move:"<?= we_message_reporting::prepareMsgForJS(g_l('alert', '[nothing_to_move]')) ?>",
				notValidFolder:"<?= we_message_reporting::prepareMsgForJS(g_l('weClass', '[notValidFolder]')) ?>",
				save_error_fields_value_not_valid: "<?= we_message_reporting::prepareMsgForJS(g_l('alert', '[save_error_fields_value_not_valid]')); ?>",
				name_nok:"<?= we_message_reporting::prepareMsgForJS(g_l('alert', '[name_nok]')); ?>",
				prefs_saved_successfully: "<?= we_message_reporting::prepareMsgForJS(g_l('cockpit', '[prefs_saved_successfully]')); ?>",
				folder_copy_success: "<?= we_message_reporting::prepareMsgForJS(g_l('copyFolder', '[copy_success]')); ?>",
				close_include:"<?= we_message_reporting::prepareMsgForJS(g_l('SEEM', '[alert][close_include]')); ?>",
				untitled:"<?= g_l('global', '[untitled]'); ?>",
			},
			message_reporting:{
				notice:"<?= g_l('alert', '[notice]'); ?>",
				warning:"<?= g_l('alert', '[warning]'); ?>",
				error:"<?= g_l('alert', '[error]'); ?>",
				msgNotice:"<?= g_l('messageConsole', '[iconBar][notice]'); ?>",
				msgWarning:"<?= g_l('messageConsole', '[iconBar][warning]'); ?>",
				msgError:"<?= g_l('messageConsole', '[iconBar][error]'); ?>",
			},
			alert:{
				browser_crashed: '<?= we_message_reporting::prepareMsgForJS(g_l('alert', '[browser_crashed]')); ?>',
				confirm_applyFilterFolder: "<?= g_l('alert', '[confirm][applyWeDocumentCustomerFiltersFolder]'); ?>",
				confirm_applyFilterDocument: "<?= g_l('alert', '[confirm][applyWeDocumentCustomerFiltersDocument]'); ?>",
				copy_folder_not_valid: "<?= we_message_reporting::prepareMsgForJS(g_l('alert', '[copy_folder_not_valid]')); ?>",
				exit_multi_doc_question: "<?= g_l('alert', '[exit_multi_doc_question]'); ?>",
				in_wf_warning:{
					tblFile:"<?= (defined('WORKFLOW_TABLE') ? g_l('alert', '[tblFile][in_wf_warning]') : ''); ?>",
					tblObjectFiles:"<?= (defined('WORKFLOW_TABLE') ? g_l('alert', '[tblObjectFiles][in_wf_warning]') : ''); ?>",
					tblObject:"<?= (defined('WORKFLOW_TABLE') ? g_l('alert', '[tblObject][in_wf_warning]') : ''); ?>",
					tblTemplates:"<?= (defined('WORKFLOW_TABLE') ? g_l('alert', '[tblTemplates][in_wf_warning]') : ''); ?>",
					tblVFiles:"<?= (defined('WORKFLOW_TABLE') ? g_l('alert', '[tblVFiles][in_wf_warning]') : ''); ?>",
				},
				move:"<?= g_l('alert', '[move]'); ?>",
				move_exit_open_docs_question:"<?= g_l('alert', '[move_exit_open_docs_question]'); ?>",
				move_exit_open_docs_continue:"<?= g_l('alert', '[move_exit_open_docs_continue]'); ?>",
			},
			cockpit:{
				increase_size: '<?= g_l('cockpit', '[increase_size]'); ?>',
				not_activated: '<?= we_message_reporting::prepareMsgForJS(g_l('alert', '[cockpit_not_activated]')); ?>',
				pre_remove: '<?= g_l('cockpit', '[pre_remove]'); ?>"',
				post_remove: '" <?= g_l('cockpit', '[post_remove]'); ?>',
				reduce_size: '<?= g_l('cockpit', '[reduce_size]') ?>',
				reset_settings: '<?= g_l('alert', '[cockpit_reset_settings]'); ?>',
			},
			editorScript:{
				confirm_navDel: "<?= g_l('navigation', '[del_question]'); ?>",
				gdTypeNotSupported: "<?= g_l('weClass', '[type_not_supported_hint]'); ?>",
				noRotate: "<?= we_message_reporting::prepareMsgForJS(g_l('weClass', '[rotate_hint]')); ?>",
				field_int_value_to_height: "<?= g_l('alert', '[field_int_value_to_height]'); ?>",
				field_contains_incorrect_chars: '<?= g_l('alert', '[field_contains_incorrect_chars]'); ?>',
				field_input_contains_incorrect_length: '<?= g_l('alert', '[field_input_contains_incorrect_length]'); ?>',
				field_int_contains_incorrect_length: '<?= g_l('alert', '[field_int_contains_incorrect_length]'); ?>',
				fieldNameNotValid: '<?= g_l('modules_object', '[fieldNameNotValid]'); ?>',
				fieldNameNotTitleDesc: '<?= g_l('modules_object', '[fieldNameNotTitleDesc]'); ?>',
				fieldNameEmpty: '<?= g_l('modules_object', '[fieldNameEmpty]'); ?>'
			},
			selectors:{
			},
			tinyMceTranslationObject: {
				<?= array_search($GLOBALS['WE_LANGUAGE'], getWELangs()); ?>:{
					we:{
						group_link:"<?= g_l('wysiwyg', '[links]'); ?>", //(insert_hyperlink)
						group_copypaste:"<?= g_l('wysiwyg', '[import_text]'); ?>",
						group_advanced:"<?= g_l('wysiwyg', '[advanced]'); ?>",
						group_insert:"<?= g_l('wysiwyg', '[insert]'); ?>",
						group_indent:"<?= g_l('wysiwyg', '[indent]'); ?>",
						//group_view:"<?= g_l('wysiwyg', '[view]'); ?>",
						group_table:"<?= g_l('wysiwyg', '[table]'); ?>",
						group_edit:"<?= g_l('wysiwyg', '[edit]'); ?>",
						group_layer:"<?= g_l('wysiwyg', '[layer]'); ?>",
						group_xhtml:"<?= g_l('wysiwyg', '[xhtml_extras]'); ?>",
						tt_weinsertbreak:"<?= g_l('wysiwyg', '[insert_br]'); ?>",
						tt_welink:"<?= g_l('wysiwyg', '[hyperlink]'); ?>",
						tt_weimage:"<?= g_l('wysiwyg', '[insert_edit_image]'); ?>",
						tt_wefullscreen_set:"<?= g_l('wysiwyg', '[maxsize_set]'); //($this->isInPopup ? g_l('wysiwyg', '[maxsize_set]') : g_l('wysiwyg', '[fullscreen]'));                    ?>",
						tt_wefullscreen_reset:"<?= g_l('wysiwyg', '[maxsize_reset]'); ?>",
						tt_welang:"<?= g_l('wysiwyg', '[language]'); ?>",
						tt_wespellchecker:"<?= g_l('wysiwyg', '[spellcheck]'); ?>",
						tt_wevisualaid:"<?= g_l('wysiwyg', '[visualaid]'); ?>",
						tt_wegallery:"<?= g_l('wysiwyg', '[addGallery]'); ?>",
						plugin_wegallery_values_nok:"<?= g_l('wysiwyg', '[gallery_alert_values_nok]'); ?>",
						cm_inserttable:"<?= g_l('wysiwyg', '[insert_table]'); ?>",
						cm_table_props:"<?= g_l('wysiwyg', '[edit_table]'); ?>",
					}
				}
			},
<?php
foreach($jsmods as $mod){
	echo $mod . ':{},';
}
?>
		},
		global:{
			WE_EDITPAGE_CONTENT:<?= we_base_constants::WE_EDITPAGE_CONTENT; ?>,
			PING_TIME:<?= (we_base_constants::PING_TIME * 1000); ?>,
		},
		message:{
			WE_MESSAGE_INFO: <?= we_message_reporting::WE_MESSAGE_INFO; ?>,
			WE_MESSAGE_FRONTEND: <?= we_message_reporting::WE_MESSAGE_FRONTEND; ?>,
			WE_MESSAGE_NOTICE:<?= we_message_reporting::WE_MESSAGE_NOTICE; ?>,
			WE_MESSAGE_WARNING:<?= we_message_reporting::WE_MESSAGE_WARNING; ?>,
			WE_MESSAGE_ERROR:<?= we_message_reporting::WE_MESSAGE_ERROR; ?>,
		},
		tables: {
			TBL_PREFIX: '<?= TBL_PREFIX; ?>',
			OBJECT_FILES_TABLE: "<?= defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'; ?>",
			OBJECT_TABLE: "<?= defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'; ?>",
<?php
foreach(we_base_request::getAllTables() as $k => $v){
	echo $k . ':"' . $v . '",';
}
?>
		},
		size:{
			tree: {
				hidden:<?= we_tree_base::HiddenWidth; ?>,
				defaultWidth:<?= we_tree_base::DefaultWidth; ?>,
				min:<?= we_tree_base::MinWidth; ?>,
				max:<?= we_tree_base::MaxWidth; ?>,
				step:<?= we_tree_base::StepWidth; ?>,
				moveWidth:<?= we_tree_base::MoveWidth; ?>,
				deleteWidth:<?= we_tree_base::DeleteWidth; ?>
			},
			catSelect: {
				width:<?= we_selector_file::WINDOW_CATSELECTOR_WIDTH; ?>,
				height:<?= we_selector_file::WINDOW_CATSELECTOR_HEIGHT; ?>
			},
			docSelect: {
				width:<?= we_selector_file::WINDOW_DOCSELECTOR_WIDTH; ?>,
				height:<?= we_selector_file::WINDOW_DOCSELECTOR_HEIGHT; ?>
			},
			windowSelect: {
				width:<?= we_selector_file::WINDOW_SELECTOR_WIDTH; ?>,
				height:<?= we_selector_file::WINDOW_SELECTOR_HEIGHT; ?>
			},
			windowDirSelect: {
				width:<?= we_selector_file::WINDOW_DIRSELECTOR_WIDTH; ?>,
				height:<?= we_selector_file::WINDOW_DIRSELECTOR_HEIGHT; ?>
			},
			windowDelSelect: {
				width:<?= we_selector_file::WINDOW_DELSELECTOR_WIDTH; ?>,
				height:<?= we_selector_file::WINDOW_DELSELECTOR_HEIGHT; ?>
			},
			sidebar: {
				defaultWidth:<?= intval(defined('SIDEBAR_DEFAULT_WIDTH') ? SIDEBAR_DEFAULT_WIDTH : 0); ?>
			}
		},
		linkPrefix: {
			TYPE_OBJ_PREFIX: '<?= we_base_link::TYPE_OBJ_PREFIX; ?>',
			TYPE_INT_PREFIX: '<?= we_base_link::TYPE_INT_PREFIX; ?>',
			TYPE_MAIL_PREFIX: '<?= we_base_link::TYPE_MAIL_PREFIX; ?>',
			TYPE_THUMB_PREFIX: '<?= we_base_link::TYPE_THUMB_PREFIX; ?>',
		},
		graphic:{
			gdSupportedTypes:{<?= implode(',', array_map(function($v){
return '"' . $v . '" : true';
}, we_base_imageEdit::supported_image_types()));
?>},
			canRotate:<?= intval(function_exists("ImageRotate")); ?>,
		}
	},
	//all relevant settings for current session
	session:{
		seemode:<?= intval($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE); ?>,
		seeMode_edit_include:<?= (!empty($SEEM_edit_include)) ? 'true' : 'false'; ?>, // in edit_include mode of seeMode
		userID:<?= $_SESSION['user']["ID"]; ?>,
		//permissions set for the user
		permissions:{
<?php
foreach($_SESSION['perms'] as $perm => $access){
	echo $perm . ':' . (!empty($_SESSION['perms']['ADMINISTRATOR']) ? 1 : intval($access)) . ',';
}
?>
		},
		sess_id:"<?= session_id(); ?>",
		specialUnload:specialUnload,
		docuLang:"<?= ($GLOBALS["WE_LANGUAGE"] === 'Deutsch' ? 'de' : 'en'); ?>",
		helpLang:"<?= $GLOBALS["WE_LANGUAGE"]; ?>",
		messageSettings:<?= (!empty($_SESSION['prefs']['message_reporting']) ? we_message_reporting::WE_MESSAGE_INFO | we_message_reporting::WE_MESSAGE_ERROR | $_SESSION['prefs']['message_reporting'] : PHP_INT_MAX); ?>,
		isChrome:<?= intval(we_base_browserDetect::isChrome()); ?>,
	},
	layout:{
		//vtabs:Vtabs,
		button:null,
		sidebar:null,
		cockpitFrame:null,
		windows:[],
		focusedWindow:null
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
 YAHOO_FILES .
 we_html_element::jsScript(JS_DIR . 'keyListener.js', 'WE().handler.dealWithKeyboardShortCut = dealWithKeyboardShortCut;') .
 we_html_element::jsScript(JS_DIR . 'windows.js', 'WE().util.jsWindow = jsWindow;WE().util.jsWindow;') .
 we_html_element::jsScript(JS_DIR . 'we_tabs/we_tabs.js') .
 we_html_element::jsScript(JS_DIR . 'messageConsole.js') .
 we_html_element::jsScript(JS_DIR . 'webEdition.js') .
 we_html_element::jsScript(JS_DIR . 'weSidebar.js') .
 we_html_element::jsScript(JS_DIR . 'weButton.js') .
 we_html_element::jsScript(JS_DIR . 'we_users_ping.js') .
 we_html_element::jsScript(JS_DIR . 'we_lcmd.js') .
 we_main_headermenu::css() .
 we_html_element::cssLink(CSS_DIR . 'sidebar.css');

foreach($jsCmd as $cur){
	echo we_html_element::jsScript($cur);
}
?>
<script><!--
function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	//	When coming from a we_cmd, always mark the document as opened with we !!!!
	if (WE().layout.weEditorFrameController.getActiveDocumentReference) {

	switch (args[0]){
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
	we_showInNewTab(args, url);
	}

	}

	function startMsg(){
<?= we_main_headermenu::createMessageConsole('mainWindow', true); ?>
	}
	function updateCheck(){
<?php
if(!empty($_SESSION['perms']['ADMINISTRATOR']) && ($versionInfo = updateAvailable())){
	?>top.we_showMessage("<?php printf(g_l('sysinfo', '[newWEAvailable]'), $versionInfo['dotted'] . ' (svn ' . $versionInfo['svnrevision'] . ')', $versionInfo['date']); ?>", WE().consts.message.WE_MESSAGE_INFO, window);
<?php }
?>
	}

//-->
</script>
</head>
<body id="weMainBody" onload="initWE(); top.start('<?= $table_to_load; ?>'); startMsg(); updateCheck();self.focus();" onbeforeunload ="return doUnload();">
	<div id="headerDiv"><?php
		$SEEM_edit_include = we_base_request::_(we_base_request::BOOL, 'SEEM_edit_include');
		$msg = (defined('MESSAGING_SYSTEM') && !$SEEM_edit_include);
		?>
		<div id="weMainHeader"><?php
			we_main_headermenu::pbody($msg);
			?>
		</div>
	</div>
	<div id="resizeFrame"><?php
		$sidebarwidth = getSidebarWidth();
		switch($_SESSION['weS']['we_mode']){
			default:
			case we_base_constants::MODE_NORMAL:
				$treewidth = isset($_COOKIE["treewidth_main"]) && ($_COOKIE["treewidth_main"] >= we_tree_base::MinWidth) ? intval($_COOKIE["treewidth_main"]) : we_tree_base::DefaultWidth;
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
		<div style="width:<?= $treewidth; ?>px;<?= $treeStyle; ?>" id="bframeDiv">
			<?php include(WE_INCLUDES_PATH . 'baumFrame.inc.php'); ?>
		</div>
		<div style="right:<?= $sidebarwidth; ?>px;left:<?= $treewidth; ?>px;" id="bm_content_frameDiv">
			<iframe src="<?= WEBEDITION_DIR; ?>multiContentFrame.php" name="bm_content_frame"></iframe>
		</div>
		<?php
		if(!(SIDEBAR_DISABLED == 1)){
			?>
			<div style="width:<?= $sidebarwidth; ?>px;" id="sidebarDiv">
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
		<iframe src="about:blank" name="plugin"></iframe>
	</div>
	<?php
//	get the frameset for the actual mode.
	echo ((defined('MESSAGING_SYSTEM') && !$SEEM_edit_include) ?
		we_messaging_headerMsg::getJS() : '') .
//	get the Treefunctions for docselector
	getWebEdition_Tree();
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
