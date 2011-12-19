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

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::htmlTop();
print STYLESHEET;
print we_htmlElement::cssElement('div#rss,div#rss *{color:black;font-size:' . (($SYSTEM == "MAC") ? "10px" : (($SYSTEM == "X11") ? "12px" : "11px")) . ';font-family:' . g_l('css','[font_family]') . ';}');
print '</head><body bgcolor="#F1F5FF">';

$rss = new XML_RSS($_SESSION["prefs"]["cockpit_rss_feed_url"], $GLOBALS['WE_BACKENDCHARSET']);
$rss->parse();
$rss_out = '<div id="rss">';
foreach ($rss->getItems() as $item) {
	$rss_out .= "<b>" . $item['title'] . "</b><p>" . $item['description'] . " ";
	if (isset($item['link']) && !empty($item['link']))
		$rss_out .= "<a href=\"" . $item['link'] . "\" target=\"_blank\">" . g_l('cockpit','[more]') . "</a>";
	$rss_out .= "</p>";
	$rss_out .= we_html_tools::getPixel(1, 10) . we_htmlElement::htmlBr();
}
$rss_out .= '</div>';
print $rss_out;
print '</body></html>';