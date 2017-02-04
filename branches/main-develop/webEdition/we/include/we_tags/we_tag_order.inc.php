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
function we_parse_tag_order($attribs, $content){
	return '<?php ' . (strpos($content, '$lv') !== false ? 'global $lv;' : '') .
		'if(' . we_tag_tagParser::printTag('order', $attribs) . '){?>' . $content . '<?php }
		we_post_tag_listview(); ?>';
}

function we_tag_order(array $attribs){
	if(!defined('WE_SHOP_VAT_TABLE')){
		echo modulFehltError('Shop', __FUNCTION__);
		return false;
	}

	$condition = weTag_getAttribute("condition", $attribs, 0, we_base_request::RAW);
	$id = weTag_getAttribute("id", $attribs, we_base_request::_(we_base_request::INT, 'we_orderid', 0), we_base_request::INT);

	$hidedirindex = weTag_getAttribute("hidedirindex", $attribs, TAGLINKS_DIRECTORYINDEX_HIDE, we_base_request::BOOL);


	if($id){
		$unique = md5(uniqid(__FILE__, true));

		$lv = new we_listview_shopOrder($unique, 1, 0, "", 0, '(IntOrderID=' . intval($id) . ')' . ($condition ? ' AND ' . $condition : ''), '', 0, $hidedirindex);
		we_pre_tag_listview($lv);
		return $lv->next_record();
	}

	we_pre_tag_listview(new stdClass());
	return false;
}
