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
			return we_html_element::jsScript(JS_DIR . 'treeSeeMode.js');
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
var busy = 0;
var hot = 0;
var last = 0;
var lastUsedLoadFrame = null;
var nlHTMLMail = false;
var browserwind = null;
var weplugin_wait = null;
// seeMode
// TODO: move to some JS-file
var dd = {
	dataTransfer: {
		text: ''
	}
};
//-->
</script>
<?php
$const = [
	'g_l' => [],
	'contentTypes' => [
		'TEMPLATE' => we_base_ContentTypes::TEMPLATE,
		'WEDOCUMENT' => we_base_ContentTypes::WEDOCUMENT,
		'OBJECT_FILE' => we_base_ContentTypes::OBJECT_FILE,
		'IMAGE' => we_base_ContentTypes::IMAGE,
		'HTML' => we_base_ContentTypes::HTML,
		'FLASH' => we_base_ContentTypes::FLASH,
		'VIDEO' => we_base_ContentTypes::VIDEO,
		'AUDIO' => we_base_ContentTypes::AUDIO,
		'JS' => we_base_ContentTypes::JS,
		'TEXT' => we_base_ContentTypes::TEXT,
		'XML' => we_base_ContentTypes::XML,
		'HTACCESS' => we_base_ContentTypes::HTACCESS,
		'CSS' => we_base_ContentTypes::CSS,
		'APPLICATION' => we_base_ContentTypes::APPLICATION,
		'COLLECTION' => we_base_ContentTypes::COLLECTION,
	],
	'dirs' => [
		'WEBEDITION_DIR' => WEBEDITION_DIR,
		'WE_MODULES_DIR' => WE_MODULES_DIR,
		'WE_MESSAGING_MODULE_DIR' => defined('WE_MESSAGING_MODULE_DIR') ? WE_MESSAGING_MODULE_DIR : '',
		'WE_INCLUDES_DIR' => WE_INCLUDES_DIR,
		'WE_JS_TINYMCE_DIR' => WE_JS_TINYMCE_DIR,
		'WE_SPELLCHECKER_MODULE_DIR' => defined('SPELLCHECKER') ? WE_SPELLCHECKER_MODULE_DIR : '',
	],
	'global' => [
		'WE_EDITPAGE_CONTENT' => we_base_constants::WE_EDITPAGE_CONTENT,
		'WE_EDITPAGE_PREVIEW' => we_base_constants::WE_EDITPAGE_PREVIEW,
		'WE_EDITPAGE_PREVIEW_TEMPLATE' => we_base_constants::WE_EDITPAGE_PREVIEW_TEMPLATE,
		'PING_TIME' => (we_base_constants::PING_TIME * 1000),
		'DEFAULT_DYNAMIC_EXT' => DEFAULT_DYNAMIC_EXT,
		'DEFAULT_STATIC_EXT' => DEFAULT_STATIC_EXT,
	],
	'message' => [
		'WE_MESSAGE_INFO' => we_message_reporting::WE_MESSAGE_INFO,
		'WE_MESSAGE_FRONTEND' => we_message_reporting::WE_MESSAGE_FRONTEND,
		'WE_MESSAGE_NOTICE' => we_message_reporting::WE_MESSAGE_NOTICE,
		'WE_MESSAGE_WARNING' => we_message_reporting::WE_MESSAGE_WARNING,
		'WE_MESSAGE_ERROR' => we_message_reporting::WE_MESSAGE_ERROR,
	],
	'linkPrefix' => [
		'TYPE_OBJ_PREFIX' => we_base_link::TYPE_OBJ_PREFIX,
		'TYPE_INT_PREFIX' => we_base_link::TYPE_INT_PREFIX,
		'TYPE_MAIL_PREFIX' => we_base_link::TYPE_MAIL_PREFIX,
		'TYPE_THUMB_PREFIX' => we_base_link::TYPE_THUMB_PREFIX,
		'EMPTY_EXT' => we_base_link::EMPTY_EXT,
		'TYPE_INT' => we_base_link::TYPE_INT,
	],
	'size' => [
		'tree' => [
			'hidden' => we_tree_base::HiddenWidth,
			'defaultWidth' => we_tree_base::DefaultWidth,
			'min' => we_tree_base::MinWidth,
			'max' => we_tree_base::MaxWidth,
			'step' => we_tree_base::StepWidth,
			'moveWidth' => we_tree_base::MoveWidth,
			'deleteWidth' => we_tree_base::DeleteWidth,
		],
		'catSelect' => [
			'width' => we_selector_file::WINDOW_CATSELECTOR_WIDTH,
			'height' => we_selector_file::WINDOW_CATSELECTOR_HEIGHT,
		],
		'docSelect' => [
			'width' => we_selector_file::WINDOW_DOCSELECTOR_WIDTH,
			'height' => we_selector_file::WINDOW_DOCSELECTOR_HEIGHT,
		],
		'windowSelect' => [
			'width' => we_selector_file::WINDOW_SELECTOR_WIDTH,
			'height' => we_selector_file::WINDOW_SELECTOR_HEIGHT,
		],
		'windowDirSelect' => [
			'width' => we_selector_file::WINDOW_DIRSELECTOR_WIDTH,
			'height' => we_selector_file::WINDOW_DIRSELECTOR_HEIGHT,
		],
		'windowDelSelect' => [
			'width' => we_selector_file::WINDOW_DELSELECTOR_WIDTH,
			'height' => we_selector_file::WINDOW_DELSELECTOR_HEIGHT,
		],
		'sidebar' => [
			'defaultWidth' => intval(defined('SIDEBAR_DEFAULT_WIDTH') ? SIDEBAR_DEFAULT_WIDTH : 0),
		]
	],
	'tables' => [
		'TBL_PREFIX' => TBL_PREFIX,
		'OBJECT_FILES_TABLE' => defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE',
		'OBJECT_TABLE' => defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE',
	],
	'graphic' => [
		'gdSupportedTypes' => [],
		'canRotate' => intval(function_exists("ImageRotate")),
	],
	'tabs' => [
		'PREVIEW' => we_base_constants::WE_EDITPAGE_PREVIEW
	]
];
foreach(we_base_imageEdit::supported_image_types() as $v){
	$const['graphic']['gdSupportedTypes'][$v] = true;
}


foreach(we_base_request::getAllTables() as $k => $v){
	$const['tables'][$k] = $v;
}
//all relevant settings for current session
$session = [
	'seemode' => intval($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE),
	'seeMode_edit_include' => (!empty($SEEM_edit_include)), // in edit_include mode of seeMode
	'userID' => $_SESSION['user']["ID"],
//permissions set for the user
	'permissions' => [],
	'sess_id' => session_id(),
	'specialUnload' => intval(!(we_base_browserDetect::isChrome() || we_base_browserDetect::isSafari())),
	'docuLang' => ($GLOBALS["WE_LANGUAGE"] === 'Deutsch' ? 'de' : 'en'),
	'helpLang' => $GLOBALS["WE_LANGUAGE"],
	'messageSettings' => (!empty($_SESSION['prefs']['message_reporting']) ? we_message_reporting::WE_MESSAGE_INFO | we_message_reporting::WE_MESSAGE_ERROR | $_SESSION['prefs']['message_reporting'] : PHP_INT_MAX),
	'isChrome' => we_base_browserDetect::isChrome(),
];
foreach($_SESSION['perms'] as $perm => $access){
	$session['permissions'][$perm] = (!empty($_SESSION['perms']['ADMINISTRATOR']) ? 1 : intval($access));
}

echo we_html_element::jsScript(JS_DIR . 'webEdition.js', '', [ 'id' => 'loadWEData',
	'data-session' => setDynamicVar($session),
	'data-consts' => setDynamicVar($const),
]) .
 we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'weTinyMceDialogs.js') .
 we_html_element::jsScript(JS_DIR . 'weNavigationHistory.js', 'WE().layout.weNavigationHistory = new weNavigationHistory();') .
 YAHOO_FILES .
 we_html_element::jsScript(JS_DIR . 'keyListener.js', 'WE().handler.dealWithKeyboardShortCut = dealWithKeyboardShortCut;') .
 we_html_element::jsScript(JS_DIR . 'windows.js', 'WE().util.jsWindow = jsWindow;WE().util.jsWindow;') .
 we_html_element::jsScript(JS_DIR . 'we_tabs/we_tabs.js') .
 we_html_element::jsScript(JS_DIR . 'messageConsole.js') .
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

			switch (args[0]) {
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
				return new (WE().util.jsWindow)(window, url, "exit_doc_question", -1, -1, 380, 130, true, false, true);
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

	function startMsg() {
<?= we_main_headermenu::createMessageConsole('mainWindow', true); ?>
	}
	function updateCheck() {
<?php
if(!empty($_SESSION['perms']['ADMINISTRATOR']) && ($versionInfo = updateAvailable())){
	?>top.we_showMessage("<?php printf(g_l('sysinfo', '[newWEAvailable]'), $versionInfo['dotted'] . ' (svn ' . $versionInfo['svnrevision'] . ')', $versionInfo['date']); ?>", WE().consts.message.WE_MESSAGE_INFO, window);
<?php }
?>
	}

//-->
</script>
</head>
<body id="weMainBody" onload="initWE(); top.start('<?= $table_to_load; ?>');
		startMsg();
		updateCheck();
		self.focus();" onbeforeunload ="return doUnload();">
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
