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
function we_parse_tag_metadata($attribs, $content, array $arr){
	if(($foo = attributFehltError($arr, 'name', __FUNCTION__)) && attributFehltError($arr, 'id', __FUNCTION__)){
		return $foo;
	}
	return '<?php if(' . we_tag_tagParser::printTag('metadata', $attribs) . '){?>' . $content . '<?php } we_post_tag_listview();?>';
}

function we_tag_metadata($attribs){
	$name = weTag_getAttribute("name", $attribs, '', we_base_request::STRING);
	$id = weTag_getAttribute("id", $attribs, 0, we_base_request::INT);

	if(!$id && $name){
		$unique = md5(uniqid(__FILE__, true));
		$_value = (isset($GLOBALS["lv"]) ?
						$GLOBALS['lv']->f($name) :
						// determine the id of the element
						($GLOBALS['we_doc']->getElement($name, 'bdid')? :
								$GLOBALS['we_doc']->getElement($name)
						)
				);

		// it is an id
		$id = (is_numeric($_value) ? $_value : 0);
	}

	if($id){
		$GLOBALS['lv'] = new we_listview_document($unique, 1, 0, "", false, "", "", false, false, 0, "", "", false, "", "", "", "", "", "", "off", true, "", $id, '', false, false, 0);
		$avail = ($GLOBALS['lv']->next_record());
	} else {
		$GLOBALS['lv'] = new stdClass();
		$avail = false;
	}


	we_pre_tag_listview();
	return $avail;
}
