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

echo we_html_tools::getHtmlTop() .
 we_html_element::jsScript(JS_DIR . 'windows.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/yahoo-min.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/event-min.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/connection-min.js');

require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');

$headCal = we_html_element::cssLink(JS_DIR . "jscalendar/skins/aqua/theme.css") .
	we_html_element::jsScript(JS_DIR . "jscalendar/calendar.js") .
	we_html_element::jsScript(WE_INCLUDES_DIR . "we_language/" . $GLOBALS["WE_LANGUAGE"] . "/calendar.js") .
	we_html_element::jsScript(JS_DIR . "jscalendar/calendar-setup.js");

echo $headCal .
 doclistView::getSearchJS() .
 STYLESHEET
?>
</head>

<body class="weEditorBody" onunload="doUnload()" onkeypress="javascript:if (event.keyCode == 13 || event.keyCode == 3)
			search(true);" onload="setTimeout('init();', 200)" onresize="sizeScrollContent();">
	<div id="mouseOverDivs_doclist"></div>
	<form name="we_form" action="" onsubmit="return false;" style="padding:0px;margin:0px;"><?php
		$view = new we_search_view();
		$content = doclistView::searchProperties($GLOBALS['we_doc']->Table);
		$headline = doclistView::makeHeadLines($GLOBALS['we_doc']->Table);
		$foundItems = (isset($_SESSION['weS']['weSearch']['foundItems'])) ? $_SESSION['weS']['weSearch']['foundItems'] : 0;
		$_parts = array(
			array("html" => doclistView::getSearchDialog()),
			array("html" => "<div id='parametersTop'>" . doclistView::getSearchParameterTop($foundItems) . '</div>' . $view->tblList($content, $headline, "doclist") . "<div id='parametersBottom'>" . doclistView::getSearchParameterBottom($GLOBALS['we_doc']->Table,$foundItems) . "</div>"),
		);

		echo doclistView::getHTMLforDoclist($_parts);
		?>
		<input type="hidden" name="obj" value="1"/>
		<input type="hidden" name="we_complete_request" value="1"/>
	</form>
</body>
</html>