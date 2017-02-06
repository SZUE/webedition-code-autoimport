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

we_html_tools::protect();

$title = 'webEdition ';
$tool = we_base_request::_(we_base_request::STRING, 'tool');
$cmd = '';

$showHeader = true;
switch($tool){
	case '':
		break;
	case 'weSearch':
		$title .= g_l('tools', '[tools]') . ' - ' . g_l('searchtool', '[weSearch]');
		$showHeader = false;
		break;
	default:
		$translate = we_core_Local::addTranslation('apps.xml');
		we_core_Local::addTranslation('default.xml', $tool);
		$title .= $translate->_('Applications') . ' - ' . $translate->_($tool);
		break;
}

if($tool === "weSearch"){
	unset($_SESSION['weS'][$tool . '_session']);

	$keyword = '';
	if(($cmd1 = we_base_request::_(we_base_request::STRING, 'we_cmd', false, 1))){
		$_SESSION['weS']['weSearch']['keyword'] = $cmd1;
		$keyword = $cmd1;
	}

	switch(($cmd2 = we_base_request::_(we_base_request::TABLE, 'we_cmd', "", 2))){//FIXME: bad to have different types at one query
		case FILE_TABLE:
			$tab = 1;
			$table = 1;
			$_SESSION['weS']['weSearch']["checkWhich"] = 1;
			$cmd = 'tool_weSearch_new_forDocuments';
			break;
		case TEMPLATES_TABLE:
			$tab = 2;
			$table = 2;
			$_SESSION['weS']['weSearch']["checkWhich"] = 2;
			$cmd = 'tool_weSearch_new_forTemplates';
			break;
		case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
			$tab = 3;
			$table = 3;
			$_SESSION['weS']['weSearch']["checkWhich"] = 3;
			$cmd = 'tool_weSearch_new_forObjects';
			break;
		case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
			$tab = 3;
			$table = 4;
			$_SESSION['weS']['weSearch']["checkWhich"] = 4;
			$cmd = 'tool_weSearch_new_forClasses';
			break;
		default:
			$tab = we_base_request::_(we_base_request::INT, 'we_cmd', 1, 4);
			$table = 5;
			$cmd = 'tool_weSearch_new';
	}

	$modelid = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 3);
} else {
	$tab = $modelid = false;
}

echo we_html_tools::getHtmlTop($title, '', 'frameset') .
 STYLESHEET .
 we_html_element::jsScript(JS_DIR . 'toolframe.js') .
 YAHOO_FILES .
 we_tabs::getHeader();
?>
</head>
<body style="overflow:hidden;" <?php echo ($showHeader ? 'onload="weTabs.setFrameSize();"' : ''); ?>><?php
	$_REQUEST['tool'] = $tool;
	echo ($showHeader ?
			we_html_element::htmlExIFrame('navi', WE_INCLUDES_PATH . 'we_tools/tools_header.inc.php', 'right:0px;') : '') .
	we_html_element::htmlIFrame('content', WE_INCLUDES_DIR . 'we_tools/tools_content.php?tool=' . $tool . ($modelid ? ('&modelid=' . $modelid) : '') . ($tab ? ('&tab=' . $tab) : '') . ($tool === 'weSearch' ? '&cmd=' . $cmd . '&keyword=' . $keyword : ''), 'position:absolute;top:' . ($showHeader ? 21 : 0) . 'px;left:0px;right:0px;bottom:0px;border-top:1px solid #999999;', '', '', false);
	?>
</body>
</html>