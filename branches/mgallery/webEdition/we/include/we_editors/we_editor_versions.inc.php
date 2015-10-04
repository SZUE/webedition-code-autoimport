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
 we_html_element::jsScript(LIB_DIR . 'additional/yui/yahoo-min.js') .
 we_html_element::jsScript(LIB_DIR . 'additional/yui/event-min.js') .
 we_html_element::jsScript(LIB_DIR . 'additional/yui/connection-min.js');

require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');

$content = $_view->getVersionsOfDoc();
$foundItems = count($content);

echo we_html_tools::getCalendarFiles() .
 $_view->getJS() .
 STYLESHEET .
 we_html_element::cssLink(CSS_DIR . 'we_versions.css', array('media' => 'screen')) .
 we_html_element::cssLink(CSS_DIR . 'we_versions_print.css', array('media' => 'print'));
?>
</head>
<body class="weEditorBody" onunload="doUnload()" onkeypress="javascript:if (event.keyCode == 13 || event.keyCode == 3)
			search(true);" onload="init();" onresize="sizeScrollContent();">
	<form name="we_form" action="" onsubmit="return false;" style="padding:0px;margin:0px;">
		<?php
		echo $_view->getHTMLforVersions(array(
			array("html" => "<div id='searchTable'>" . $_view->getBodyTop() . "</div>"),
			array("html" => "<div id='parametersTop'>" . $_view->getParameterTop($foundItems) . "</div>" . $_view->tblList($content, $_view->makeHeadLines()) . "<div id='parametersBottom'>" . $_view->getParameterBottom($foundItems) . "</div>")
		)) .
		we_html_element::htmlHidden("we_complete_request", 1);
		?>
	</form>
</body></html>