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
we_html_tools::protect();
$_view = new we_versions_view();

echo we_html_tools::getHtmlTop() .
 we_html_element::jsScript(JS_DIR . 'windows.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/yahoo-min.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/event-min.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/connection-min.js');

require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');

$content = $_view->getVersionsOfDoc();
$foundItems = count($content);

echo we_html_element::cssLink(JS_DIR . 'jscalendar/skins/aqua/theme.css') .
 we_html_element::jsScript(JS_DIR . 'jscalendar/calendar.js') .
 we_html_element::jsScript(WE_INCLUDES_DIR . 'we_language/' . $GLOBALS["WE_LANGUAGE"] . '/calendar.js') .
 we_html_element::jsScript(JS_DIR . 'jscalendar/calendar-setup.js') .
 $_view->getJS() .
 STYLESHEET .
 we_html_element::cssElement('
#scrollContent {overflow: auto; }
#searchTable {display: block; }
#eintraege_pro_seite {display: inline;margin-right:10px; }
#anzahl {display: inline;margin-right:10px;  }
#eintraege {display: none; }
#print {display: inline;}
#zurueck {display: block;}
#weiter {display: block;}
#pageselect {display: inline;}
#bottom{display: inline;}
#beschreibung_print {display: none; }
.printShow{display: block; }
#deleteVersion{display: block; }
#deleteAllVersions{display: block; }
#label_deleteAllVersions{display: block; }
#deleteButton{display: block; }', array('media' => 'screen')) .
 we_html_element::cssElement('
#scrollContent {overflow: visible; }
#searchTable {display: none; }
#eintraege_pro_seite {display: none; }
#anzahl {display: none; }
#eintraege {display: inline;margin-right:10px; }
#print {display: none;}
#zurueck {display: none;}
#weiter {display: none;}
#pageselect {display: none;}
#bottom{display: none;}
#beschreibung_print {display: block; }
.printShow{display: none; }
#deleteVersion{display: none; }
#deleteAllVersions{display: none; }
#label_deleteAllVersions{display: none; }
#deleteButton{display: none; }', array('media' => 'print'));
?>
</head>
<body class="weEditorBody" onunload="doUnload()" onkeypress="javascript:if (event.keyCode == 13 || event.keyCode == 3)
			search(true);" onload="setTimeout('init();', 200)" onresize="sizeScrollContent();">
	<form name="we_form" action="" onsubmit="return false;" style="padding:0px;margin:0px;">
		<?php
		echo $_view->getHTMLforVersions(array(
			array("html" => "<div id='searchTable'>" . $_view->getBodyTop() . "</div>"),
			array("html" => "<div id='parametersTop'>" . $_view->getParameterTop($foundItems) . "</div>" . $_view->tblList($content, $_view->makeHeadLines()) . "<div id='parametersBottom'>" . $_view->getParameterBottom($foundItems) . "</div>")
		));
		?>
		<input type="hidden" name="we_complete_request" value="1"/>
	</form>
</body></html>