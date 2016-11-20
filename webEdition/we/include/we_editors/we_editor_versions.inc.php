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

$versionsView = new we_versions_view($GLOBALS['we_doc']->versionsModel);

echo we_html_tools::getHtmlTop() .
 YAHOO_FILES;

require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');

$content = $versionsView->getVersionsOfDoc();
$foundItems = count($content);

echo $versionsView->getJS() .
 we_html_element::cssLink(CSS_DIR . 'we_versions.css', ['media' => 'screen']) .
 we_html_element::cssLink(CSS_DIR . 'we_versions_print.css', ['media' => 'print']);
?>
</head>
<body class="weEditorBody" onunload="doUnload()" onkeypress="javascript:if (event.keyCode == 13 || event.keyCode == 3)
			search(true);" onload="init();" onresize="sizeScrollContent();">
	<form name="we_form" action="" onsubmit="return false;" style="padding:0px;margin:0px;">
		<?php
		echo $versionsView->getHTMLforVersions([
				["html" => "<div id='searchTable'>" . $versionsView->getBodyTop() . '</div>'],
				["html" => "<div id='parametersTop'>" . $versionsView->getParameterTop($foundItems) . '</div>' . $versionsView->tblList($content, $versionsView->makeHeadLines()) . "<div id='parametersBottom'>" . $versionsView->getParameterBottom($foundItems) . "</div>"]
		]) .
		we_html_element::htmlHidden("we_complete_request", 1);
		?>
	</form>
</body></html>