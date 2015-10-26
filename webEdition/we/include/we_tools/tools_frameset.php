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
	if(($cmd1 = we_base_request::_(we_base_request::STRING, 'we_cmd', false, 1))){
		$_SESSION['weS']['weSearch']['keyword'] = $cmd1;
	}
	//look which search is active
	switch(($cmd2 = we_base_request::_(we_base_request::TABLE, 'we_cmd', "", 2))){//FIXME: bad to have different types at one query
		case FILE_TABLE:
			$tab = 1;
			$_SESSION['weS']['weSearch']["checkWhich"] = 1;
			break;
		case TEMPLATES_TABLE:
			$tab = 2;
			$_SESSION['weS']['weSearch']["checkWhich"] = 2;
			break;
		case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
			$tab = 3;
			$_SESSION['weS']['weSearch']["checkWhich"] = 3;
			break;
		case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
			$tab = 3;
			$_SESSION['weS']['weSearch']["checkWhich"] = 4;
			break;

		default:
			$tab = we_base_request::_(we_base_request::INT, 'we_cmd', 1, 4);
	}

	$modelid = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 3);
} else {
	$tab = $modelid = false;
}

echo we_html_tools::getHtmlTop($title, '', 'frameset') .
 we_html_element::jsScript(JS_DIR . 'toolframe.js') .
 we_html_element::jsScript(LIB_DIR . 'additional/yui/yahoo-min.js') .
 we_html_element::jsScript(LIB_DIR . 'additional/yui/event-min.js') .
 we_html_element::jsScript(LIB_DIR . 'additional/yui/connection-min.js');
?>
</head>
<body style="overflow:hidden;"><?php
	$_REQUEST['tool'] = $tool;
	echo ($showHeader ?
			we_html_element::htmlExIFrame('navi', WE_INCLUDES_PATH . 'we_tools/tools_header.php', 'background-color:white;position:absolute;top:0px;height:21px;left:0px;right:0px;overflow: hidden;') : '') .
	we_html_element::htmlIFrame('content', WE_INCLUDES_DIR . 'we_tools/tools_content.php?tool=' . $tool . ($modelid ? ('&modelid=' . $modelid) : '') . ($tab ? ('&tab=' . $tab) : ''), 'position:absolute;top:' . ($showHeader ? 21 : 0) . 'px;left:0px;right:0px;bottom:0px;border-top:1px solid #999999;', '', '', false);
	?>
</body>
</html>