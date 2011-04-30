<?php

/**
 * webEdition CMS
 *
 * $Rev: 2633 $
 * $Author: mokraemer $
 * $Date: 2011-03-08 01:16:50 +0100 (Di, 08. Mär 2011) $
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
function we_parse_tag_metadata($attribs, $content) {
	eval('$arr = ' . $attribs . ';');
	if (($foo = attributFehltError($arr, 'name', 'metadata')))
		return $foo;
	return '<?php if(we_tag(\'metadata\',' . $attribs . ')){' . $content . '} we_post_tag_listview();?>';
}

function we_tag_metadata($attribs, $content) {
	$name = we_getTagAttribute("name", $attribs);

	if (!isset($GLOBALS["we_lv_array"])) {
		$GLOBALS["we_lv_array"] = array();
	}

	include_once($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/listview/metadatatag.class.php");

	$GLOBALS["lv"] = new metadatatag($name);
//$lv = clone($GLOBALS["lv"]); // for backwards compatibility
	if (is_array($GLOBALS["we_lv_array"]))
		array_push($GLOBALS["we_lv_array"], clone($GLOBALS["lv"]));

	return $GLOBALS["lv"]->avail;
}