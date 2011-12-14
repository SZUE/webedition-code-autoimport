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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */


include_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we.inc.php");

we_html_tools::protect();

we_html_tools::htmlTop ();

echo we_htmlElement::jsScript(JS_DIR.'windows.js').
	we_htmlElement::jsScript(JS_DIR.'libs/yui/yahoo-min.js').
	we_htmlElement::jsScript(JS_DIR.'libs/yui/event-min.js').
	we_htmlElement::jsScript(JS_DIR.'libs/yui/connection-min.js');

include_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_editors/we_editor_script.inc.php");

$headCal = we_htmlElement::linkElement ( array ("rel" => "stylesheet", "type" => "text/css", "href" => JS_DIR . "jscalendar/skins/aqua/theme.css", "title" => "Aqua" ) ) . we_htmlElement::jsElement ( "", array ("src" => JS_DIR . "jscalendar/calendar.js" ) ) . we_htmlElement::jsElement ( "", array ("src" => WEBEDITION_DIR . "we/include/we_language/" . $GLOBALS ["WE_LANGUAGE"] . "/calendar.js" ) ) . we_htmlElement::jsElement ( "", array ("src" => JS_DIR . "jscalendar/calendar-setup.js" ) );

echo $headCal;

$_view = new doclistView ( );

echo $_view->getSearchJS ();

print STYLESHEET;

echo '</head>

<body class="weEditorBody" onUnload="doUnload()" onkeypress="javascript:if(event.keyCode==\'13\' || event.keyCode==\'3\') search(true);" onLoad="setTimeout(\'init();\',200)" onresize="sizeScrollContent();">';

echo '<div id="mouseOverDivs_doclist"></div>';

echo '<form name="we_form" onSubmit="return false;" style="padding:0px;margin:0px;">';

$_parts = array ( );
$_parts [] = array ("html" => $_view->getSearchDialog () );
$content = $_view->searchProperties ();
$headline = $_view->makeHeadLines ();
$foundItems = (isset($_SESSION['weSearch']['foundItems'])) ? $_SESSION['weSearch']['foundItems'] : 0;
$_parts [] = array ("html" => "<div id='parametersTop'>" . $_view->getSearchParameterTop ( $foundItems ) . "</div>" . searchtoolView::tblList ( $content, $headline, "doclist" ) . "<div id='parametersBottom'>" . $_view->getSearchParameterBottom ( $foundItems ) . "</div>" );

echo $_view->getHTMLforDoclist ( $_parts );

echo '
</form>
</body>
</html>';
