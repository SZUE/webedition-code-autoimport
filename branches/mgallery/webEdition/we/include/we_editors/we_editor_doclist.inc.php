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
	we_html_element::jsScript(LIB_DIR . 'additional/yui/yahoo-min.js') .
 we_html_element::jsScript(LIB_DIR . 'additional/yui/event-min.js') .
 we_html_element::jsScript(LIB_DIR . 'additional/yui/connection-min.js');

require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');

$doclistSearch = new $GLOBALS['we_doc']->doclistSearchClass($GLOBALS['we_doc']->doclistModel);
$doclistView = new $GLOBALS['we_doc']->doclistViewClass($doclistSearch);

echo we_html_tools::getCalendarFiles() .
	$doclistView->getSearchJS() .
	STYLESHEET
?>
</head>

<body class="weEditorBody" onunload="doUnload()" onkeypress="javascript:if (event.keyCode == 13 || event.keyCode == 3) {
			search(true);
		}" onload="setTimeout(weSearch.init, 200)" onresize="weSearch.sizeScrollContent();">
	<div id="mouseOverDivs_<?php echo we_search_view::SEARCH_DOCLIST; ?>"></div>
	<form name="we_form" action="" onsubmit="return false;" style="padding:0px;margin:0px;"><?php
		$results = $doclistSearch->searchProperties();
		$content = $doclistView->makeContent($results);

		$headline = $doclistView->makeHeadLines($GLOBALS['we_doc']->Table);
		$foundItems = (isset($_SESSION['weS']['weSearch']['foundItems'])) ? $_SESSION['weS']['weSearch']['foundItems'] : 0;

		echo $doclistView->getHTMLforDoclist(array(
			array('html' => $doclistView->getSearchDialog()),
			array('html' => '<div id="parametersTop_DoclistSearch">' . $doclistView->getSearchParameterTop($foundItems) . '</div>' . $doclistView->tblList($content, $headline, "doclist") . "<div id='parametersBottom_DoclistSearch'>" . $doclistView->getSearchParameterBottom($GLOBALS['we_doc']->Table, $foundItems) . "</div>"),
		)) .
		we_html_element::htmlHiddens(array(
			'obj' => 1,
			'we_complete_request' => 1
		));
		?>
	</form>
</body>
</html>