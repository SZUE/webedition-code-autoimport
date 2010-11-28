<?php
/**
 * webEdition CMS
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

/**
 * @return string
 * @param array $attribs
 * @param string $content
 * @desc Beschreibung eingeben...
 */
function we_tag_xmlfeed($attribs, $content){

	include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/base/weFile.class.php");
	include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_exim/weXMLBrowser.class.php");

	$foo = attributFehltError($attribs, "name", "xmlfeed");
	if ($foo)
		return $foo;
	$foo = attributFehltError($attribs, "url", "xmlfeed");
	if ($foo)
		return $foo;

	$name = we_getTagAttribute("name", $attribs);
	$url = we_getTagAttribute("url", $attribs);

	if (isset($attribs["refresh"]) && is_numeric($attribs["refresh"]))
		$refresh = $attribs["refresh"] * 60;
	else
		$refresh = 0;

	if (!isset($GLOBALS["xmlfeeds"]))
		$GLOBALS["xmlfeeds"] = array();

	$GLOBALS["xmlfeeds"][$name] = new weXMLBrowser();
	$cache = $_SERVER["DOCUMENT_ROOT"] . WEBEDITION_DIR . "xmlfeeds/" . $name;

	$do_refresh = true;

	if (is_file($cache) && $refresh > 0) {
		$now = time();
		$stat = stat($cache);
		$exp = $stat["mtime"] + $refresh;
		$do_refresh = ($exp < $now);
	}

	if (!is_file($cache) || $do_refresh) {
		$GLOBALS["xmlfeeds"][$name]->getFile($url);
		$GLOBALS["xmlfeeds"][$name]->saveCache($cache, $refresh);
	} else {
		$GLOBALS["xmlfeeds"][$name]->loadCache($cache);
	}
}
