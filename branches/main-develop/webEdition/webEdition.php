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
function getWebEdition_Tree(we_base_jsCmd $jsCmd){
	$Tree = new we_tree_main($jsCmd, "webEdition.php", "top", "top", "top.load");
	switch($_SESSION['weS']['we_mode']){
		default:
		case we_base_constants::MODE_NORMAL:
			return [$Tree->getJSTreeCode(), $Tree->getHTMLConstruct()];
		case we_base_constants::MODE_SEE:
			return [we_html_element::jsScript($jsCmd, JS_DIR . 'treeSeeMode.js'), $Tree->getHTMLConstruct()];
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


if(we_base_permission::hasPerm('CAN_SEE_DOCUMENTS')){
	$table_to_load = FILE_TABLE;
} else if(we_base_permission::hasPerm('CAN_SEE_TEMPLATES')){
	$table_to_load = TEMPLATES_TABLE;
} else if(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm('CAN_SEE_OBJECTFILES')){
	$table_to_load = OBJECT_FILES_TABLE;
} else if(defined('OBJECT_TABLE') && we_base_permission::hasPerm('CAN_SEE_OBJECTS')){
	$table_to_load = OBJECT_TABLE;
} else if(we_base_permission::hasPerm('CAN_SEE_COLLECTIONS')){
	$table_to_load = VFILE_TABLE;
} else {
	$table_to_load = '';
}

$jsCmd = new we_base_jsCmd();

$jsWeCmd = [];
foreach($GLOBALS['_we_active_integrated_modules'] as $mod){
	if(file_exists(WE_JS_MODULES_PATH . $mod . '/we_webEditionCmd_' . $mod . '.js')){
		$jsWeCmd[$mod] = WE_JS_MODULES_DIR . $mod . '/we_webEditionCmd_' . $mod . '.js';
	}
}

we_tool_lookup::getJsCmdInclude($jsWeCmd);
$jsmods = array_keys($jsWeCmd);
$jsmods[] = 'base';
$diff = array_diff(array_keys(we_base_moduleInfo::getAllModules()), $GLOBALS['_we_active_integrated_modules']);

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
		'FOLDER' => we_base_ContentTypes::FOLDER
	],
	'dirs' => [
		'WEBEDITION_DIR' => WEBEDITION_DIR,
		'WE_MODULES_DIR' => WE_MODULES_DIR,
		'WE_MESSAGING_MODULE_DIR' => defined('WE_MESSAGING_MODULE_DIR') ? WE_MESSAGING_MODULE_DIR : '',
		'WE_INCLUDES_DIR' => WE_INCLUDES_DIR,
		'WE_JS_TINYMCE_DIR' => WE_JS_TINYMCE_DIR,
		'WE_SPELLCHECKER_MODULE_DIR' => defined('SPELLCHECKER') ? WE_SPELLCHECKER_MODULE_DIR : '',
	],
	'modules' => [
		'active' => $GLOBALS['_we_active_integrated_modules'],
		'jsmods' => $jsmods,
		'inactive' => $diff,
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
		'TYPE_INT' => we_base_link::TYPE_INT,
		'TYPE_EXT' => we_base_link::TYPE_EXT,
		'TYPE_ALL' => we_base_link::TYPE_ALL,
		'EMPTY_EXT' => we_base_link::EMPTY_EXT,
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
		'dialog' => [
			'tiny' => 300,
			'smaller' => 400,
			'small' => 600,
			'medium' => 800,
			'big' => 1000,
			'fullScreen' => 2500
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
		'canRotate' => intval(function_exists('ImageRotate')),
	],
	'tabs' => [
		'PREVIEW' => we_base_constants::WE_EDITPAGE_PREVIEW,
		'navigation' => [
			'PROPERTIES' => we_navigation_frames::TAB_PROPERTIES,
			'CONTENT' => we_navigation_frames::TAB_CONTENT,
			'CUSTOMER' => we_navigation_frames::TAB_CUSTOMER,
			'PREVIEW' => we_navigation_frames::TAB_PREVIEW,
		]
	],
	'import' => [
		'TYPE_CSV' => we_import_functions::TYPE_CSV,
		'TYPE_GENERIC_XML' => we_import_functions::TYPE_GENERIC_XML,
		'TYPE_WE_XML' => we_import_functions::TYPE_WE_XML,
		'TYPE_LOCAL_FILES' => we_import_functions::TYPE_LOCAL_FILES,
		'TYPE_SITE' => we_import_functions::TYPE_SITE
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
	'lang' => [
		'short' => array_search($GLOBALS['WE_LANGUAGE'], getWELangs()),
		'long' => $GLOBALS["WE_LANGUAGE"]
	],
	'messageSettings' => (!empty($_SESSION['prefs']['message_reporting']) ? we_message_reporting::WE_MESSAGE_INFO | we_message_reporting::WE_MESSAGE_ERROR | $_SESSION['prefs']['message_reporting'] : PHP_INT_MAX),
	'isChrome' => we_base_browserDetect::isChrome(),
	'isAppleTouch' => (/* we_base_browserDetect::inst()->isSafari() */ (we_base_browserDetect::inst()->getSystem() == we_base_browserDetect::SYS_IPAD || we_base_browserDetect::inst()->getSystem() == we_base_browserDetect::SYS_IPHONE)),
	'isMac' => we_base_browserDetect::isMAC(),
	'deleteMode' => 0,
];
foreach($_SESSION['perms'] as $perm => $access){
	$session['permissions'][$perm] = (!empty($_SESSION['perms']['ADMINISTRATOR']) ? 1 : intval($access));
}

$head = we_html_element::jsScript(JS_DIR . 'webEdition.js', '', ['id' => 'loadWEData',
		'data-session' => setDynamicVar($session),
		'data-consts' => setDynamicVar($const),
	]) .
	we_html_element::jsScript(JS_DIR . 'we_webEditionCmd_base.js') .
	we_html_element::jsScript(JS_DIR . 'weNavigationHistory.js', 'WE().layout.weNavigationHistory = new weNavigationHistory();') .
	JQUERY .
	we_html_element::jsScript(JS_DIR . 'keyListener.js', 'WE().handler.dealWithKeyboardShortCut = dealWithKeyboardShortCut;') .
	we_html_element::jsScript(JS_DIR . 'windows.js', 'WE().util.jsWindow = jsWindow;WE().util.jsWindow;') .
	we_html_element::jsScript(JS_DIR . 'we_tabs/we_tabs.js') .
	we_html_element::jsScript(JS_DIR . 'messageConsole.js') .
	we_html_element::jsScript(JS_DIR . 'weSidebar.js') .
	we_html_element::jsScript(JS_DIR . 'weButton.js') .
	we_html_element::jsScript(JS_DIR . 'we_users_ping.js') .
	we_html_element::jsScript(JS_DIR . 'utils/multi_edit.js') .
	we_html_element::jsScript(JS_DIR . 'weFileUpload.js') .
	we_html_element::jsScript(LIB_DIR . 'additional/ExifReader/ExifReader.js') .
	we_html_element::jsScript(LIB_DIR . 'additional/pngChunksEncode/index.js') .
	we_html_element::jsScript(LIB_DIR . 'additional/pngChunksExtract/index.js') .
	we_html_element::jsScript(LIB_DIR . 'additional/pngChunksExtract/crc32.js') .
	we_html_element::jsScript(LIB_DIR . 'additional/pica/pica.js') .
	we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'weTinyMce_config.js') .
	we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'weTinyMce_functionsTop.js') .
	we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'weTinyMce_tinyWrapper.js') .
	we_main_headermenu::css() .
	we_html_element::cssLink(CSS_DIR . 'sidebar.css');

foreach($jsWeCmd as $cur){
	$head .= we_html_element::jsScript($cur);
}

list($jsTree, $treeHtml) = getWebEdition_Tree($jsCmd);

$head .= $jsCmd->getCmds();
$versionInfo = empty($_SESSION['perms']['ADMINISTRATOR']) ? [] : updateAvailable();

echo we_html_tools::getHtmlTop('webEdition - ' . $_SESSION['user']['Username'], '', '', $head, '', false);
?>
<body id="weMainBody" onload="initWE();
		top.start('<?= $table_to_load; ?>');
		startMsg();
		checkPwd(<?= intval(empty($_SESSION['WE_USER_PASSWORD_NOT_SUFFICIENT'])); ?>);
		updateCheck(<?= (empty($versionInfo) ? '0,0,0' : '1,\'' . $versionInfo['dotted'] . ' (svn ' . $versionInfo['svnrevision'] . ')\',\'' . $versionInfo['date'] . '\'') ?>);
		self.focus();" onbeforeunload ="return doUnload();">
<dialog id="alertBox"></dialog>
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
		<div id="vtabs"><?php
			$vtab = [
				'FILE_TABLE' => [
					'show' => we_base_permission::hasPerm('CAN_SEE_DOCUMENTS'),
					'desc' => '<i class="fa fa-file-o"></i> ' . g_l('global', '[documents]'),
				],
				'TEMPLATES_TABLE' => [
					'show' => we_base_permission::hasPerm('CAN_SEE_TEMPLATES'),
					'desc' => '<i class="fa fa-file-code-o"></i> ' . g_l('global', '[templates]'),
				],
				'OBJECT_FILES_TABLE' => [
					'show' => defined('OBJECT_TABLE') && we_base_permission::hasPerm('CAN_SEE_OBJECTFILES'),
					'desc' => '<i class="fa fa-file"></i> ' . g_l('global', '[objects]'),
				],
				'OBJECT_TABLE' => [
					'show' => defined('OBJECT_TABLE') && we_base_permission::hasPerm("CAN_SEE_OBJECTS"),
					'desc' => '<i class="fa fa-chevron-left"></i><i class="fa fa-chevron-right"></i> ' . g_l('javaMenu_object', '[classes]'),
				],
				'VFILE_TABLE' => [
					'show' => we_base_moduleInfo::isActive(we_base_moduleInfo::COLLECTION) && we_base_permission::hasPerm("CAN_SEE_COLLECTIONS"),
					'desc' => '<i class="fa fa-archive"></i> ' . g_l('global', '[vfile]'),
				]
			];
			foreach($vtab as $tab => $val){
				if($val['show']){
					echo '<div class="tab tabNorm" onclick="WE().layout.vtab.click(this,\'' . constant($tab) . '\');" data-table="' . constant($tab) . '"><span class="middlefont">' . $val['desc'] . '</span></div>';
				}
			}
			?>
			<div id="baumArrows">
				<div class="baumArrow" id="incBaum" title="<?= g_l('global', '[tree][grow]'); ?>" <?= ($treewidth <= 100) ? 'style="background-color: grey"' : ''; ?> onclick="WE().layout.tree.inc();"><i class="fa fa-plus"></i></div>
				<div class="baumArrow" id="decBaum" title="<?= g_l('global', '[tree][reduce]'); ?>" <?= ($treewidth <= 100) ? 'style="background-color: grey"' : ''; ?> onclick="WE().layout.tree.dec();"><i class="fa fa-minus"></i></div>
			</div>
		</div>
		<div id="treeFrameDiv">
			<div id="treeControl">
				<span id="treeName" class="middlefont"></span>
				<span id="reloadTree" onclick="we_cmd('loadVTab', top.treeData.table, 0);"><i class="fa fa-refresh"></i></span>
				<span id="toggleTree" onclick="WE().layout.tree.toggle();" title="<?= g_l('global', '[tree][minimize]'); ?>"><i id="arrowImg" class="fa fa-lg fa-caret-<?= ($treewidth <= 100) ? "right" : "left"; ?>" ></i></span>
			</div>
			<div id="treeContent">
				<div id="bm_treeheaderDiv">
					<iframe src="about:blank" name="treeheader"></iframe>
				</div>
				<?= $treeHtml; ?>
				<div id="bm_searchField">
					<div id="infoField" class="defaultfont"></div>
					<form name="we_form" onsubmit="top.we_cmd('tool_weSearch_edit', document.we_form.keyword.value, top.treeData.table);
							return false;">
						<div id="search">
							<?php
							echo we_html_tools::htmlTextInput('keyword', 10, we_base_request::_(we_base_request::STRING, 'keyword', ''), '', 'placeholder="' . g_l('buttons_modules_message', '[search][alt]') . '"', 'search') .
							we_html_button::create_button(we_html_button::SEARCH, "javascript:top.we_cmd('tool_weSearch_edit',document.we_form.keyword.value, top.treeData.table);");
							?>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div style="right:<?= $sidebarwidth; ?>px;left:<?= $treewidth; ?>px;" id="bm_content_frameDiv">
		<iframe src="<?= WEBEDITION_DIR; ?>we_cmd.php?we_cmd[0]=loadMultiEditor" name="bm_content_frame"></iframe>
	</div>
	<?php
	if(!(SIDEBAR_DISABLED == 1)){
		?>
		<div style="width:<?= $sidebarwidth; ?>px;" id="sidebarDiv">
			<?php
			we_sidebar_frames::getHTML('');
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
	we_messaging_headerMsg::getJS() : '');
//	get the Treefunctions for docselector
?>
</body>
</html>
<?php
unset($_SESSION['WE_USER_PASSWORD_NOT_SUFFICIENT']);
if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) && (!isset($SEEM_edit_include) || !$SEEM_edit_include)){
	flush();
	if(function_exists('fastcgi_finish_request')){
		fastcgi_finish_request();
	}
	session_write_close();
// trigger scheduler
	we_schedpro::trigger_schedule();
}
