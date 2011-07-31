<?php

/**
 * webEdition CMS
 *
 * $Rev: 2836 $
 * $Author: mokraemer $
 * $Date: 2011-04-30 00:42:42 +0200 (Sa, 30. Apr 2011) $
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
function we_parse_tag_orderitem($attribs, $content) {
	return '<?php global $lv;
		if('.we_tagParser::printTag('orderitem', $attribs).'){?>' . $content . '<?php } 
		we_post_tag_listview(); ?>';
}

function we_tag_orderitem($attribs, $content) {

	if (!defined('WE_SHOP_MODULE_DIR')) {
		print modulFehltError('Shop', '"orderitem"');
		return false;
	}

	$condition = we_getTagAttribute("condition", $attribs, 0);
	$we_orderitemid = we_getTagAttribute("id", $attribs, 0);
	$we_orderid = we_getTagAttribute("orderid", $attribs, 0);

	$hidedirindex = we_getTagAttribute("hidedirindex", $attribs, (defined('TAGLINKS_DIRECTORYINDEX_HIDE') && TAGLINKS_DIRECTORYINDEX_HIDE ? "true" : "false"), false);

	if ($condition) {
		$condition = $condition . ' AND ' . "IntID = " . $we_orderitemid;
	} else {
		$condition = "IntID = " . $we_orderitemid;
	}

	if (!isset($GLOBALS["we_lv_array"])) {
		$GLOBALS["we_lv_array"] = array();
	}

	include_once(WE_SHOP_MODULE_DIR . "we_orderitemtag.inc.php");

	$we_orderitemid = $we_orderitemid ? $we_orderitemid : (isset($_REQUEST["we_orderitemid"]) ? $_REQUEST["we_orderitemid"] : 0);


	$GLOBALS["lv"] = new we_orderitemtag($we_orderitemid, $condition, $hidedirindex);
	$lv = clone($GLOBALS["lv"]); // for backwards compatibility
	if (is_array($GLOBALS["we_lv_array"])){
		array_push($GLOBALS["we_lv_array"], clone($GLOBALS["lv"]));
	}
	return $GLOBALS["lv"]->avail;
}
