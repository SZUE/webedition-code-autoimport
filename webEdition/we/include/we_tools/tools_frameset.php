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
switch($tool){
	case '':
		break;
	case 'weSearch':
		$title .= g_l('tools', '[tools]') . ' - ' . g_l('searchtool', '[weSearch]');
		break;
	case 'navigation':
		$title .= g_l('tools', '[tools]') . ' - ' . g_l('navigation', '[navigation]');
		break;
	default:
		$translate = we_core_Local::addTranslation('apps.xml');
		we_core_Local::addTranslation('default.xml', $tool);
		$title .= $translate->_('Applications') . ' - ' . $translate->_($tool);
		break;
}

echo we_html_tools::getHtmlTop($title, '', 'frameset') .
 we_html_element::jsElement('
	top.weToolWindow = true;

	function toggleBusy(){
	}
	var makeNewEntry = 0;
	var publishWhenSave = 0;


	function we_cmd() {
		args = "";
		for(var i = 0; i < arguments.length; i++) {
					args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
		}
		eval("top.content.we_cmd("+args+")");
	}
');

if($tool === "weSearch"){
	if(($cmd1 = we_base_request::_(we_base_request::STRINGC, 'we_cmd', false, 1))){
		$_SESSION['weS']['weSearch']["keyword"] = $cmd1;
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

echo we_html_element::jsScript(JS_DIR . "keyListener.js") .
 we_html_element::jsScript(JS_DIR . "libs/yui/yahoo-min.js") .
 we_html_element::jsScript(JS_DIR . "libs/yui/event-min.js") .
 we_html_element::jsScript(JS_DIR . "libs/yui/connection-min.js");
?>
</head>
<frameset rows="26,*" border="0" framespacing="0" frameborder="no">');
	<frame src="<?php echo WE_INCLUDES_DIR; ?>we_tools/tools_header.php?tool=<?php echo $tool; ?>" name="navi" noresize scrolling="no"/>
	<frame src="<?php echo WE_INCLUDES_DIR; ?>we_tools/tools_content.php?tool=<?php
	echo $tool . ($modelid ? ('&modelid=' . $modelid) : '') . ($tab ? ('&tab=' . $tab) : '');
	?>" name="content" noresize scrolling="no"/>
</frameset>
<body bgcolor="#ffffff"></body>
</html>