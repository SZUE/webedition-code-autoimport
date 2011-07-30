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

function we_tag_linklist($attribs, $content){
	include_once ($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_linklist.inc.php");
	$name = we_getTagAttribute("name", $attribs);
	$content = str_replace("we:link", "we_:_link", $content);
	$foo = attributFehltError($attribs, "name", "linklist");
	if (defined('TAGLINKS_DIRECTORYINDEX_HIDE') && TAGLINKS_DIRECTORYINDEX_HIDE){
		$hidedirindex = we_getTagAttribute("hidedirindex", $attribs, "true", true,true);
	} else {
		$hidedirindex = we_getTagAttribute("hidedirindex", $attribs, "false", true);
	}
	if (defined('TAGLINKS_OBJECTSEOURLS') && TAGLINKS_OBJECTSEOURLS){
		$objectseourls = we_getTagAttribute("objectseourls", $attribs, "true", true,true);
	} else {
		$objectseourls = we_getTagAttribute("objectseourls", $attribs, "false", true);
	}
	if ($foo)
		return $foo;
	$isInListview = isset($GLOBALS["lv"]);

	if ($isInListview) {
		$linklist = $GLOBALS["lv"]->f($name);
	} else
		if (isset($GLOBALS["we_doc"])) {
			$linklist = $GLOBALS["we_doc"]->getElement($name);
		}
	$ll = new we_linklist($linklist,$hidedirindex,$objectseourls);
	$ll->name = $name;

	$out = $ll->getHTML(
			(isset($GLOBALS["we_editmode"]) && $GLOBALS["we_editmode"] && (!$isInListview)),
			$attribs,
			$content,
			$GLOBALS["we_doc"]->Name);
	return $out;
}
