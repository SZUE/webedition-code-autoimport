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

function we_tag_ifShopField($attribs,$content) {
    
	require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tags/we_tag_shopField.inc.php');
	
	$foo = attributFehltError($attribs, "name", "ifShopField");if($foo) return $foo;
	$foo = attributFehltError($attribs, "reference", "ifShopField");if($foo) return $foo;
	$foo = attributFehltError($attribs, "shopname", "ifShopField");if($foo) return $foo;
	$foo = attributFehltError($attribs, "match", "ifShopField", true);if($foo) return $foo;
	
	
	
	$match = we_getTagAttribute("match", $attribs);
	
	$name      = we_getTagAttribute("name", $attribs);
	$reference = we_getTagAttribute("reference", $attribs);
	$shopname  = we_getTagAttribute("shopname", $attribs);
	$operator  = we_getTagAttribute("operator", $attribs);
	
	// quickfix 4192
	if (isset($GLOBALS["lv"]->BlockInside) && !$GLOBALS["lv"]->BlockInside  ){ // if due to bug 4635
		$matchA = explode("blk_",$match);
		$match = $matchA[0];
	}
	$attribs['type']='print';
	$atts = removeAttribs($attribs,array('match','operator'));
	
	$realvalue = we_tag_shopField($atts, "");
	switch ($operator) {
		case ("equal"): return $realvalue == $match; break;
		case ("less"): return $realvalue < $match; break;
		case ("less|equal"): return $realvalue <= $match; break;
		case ("greater"): return $realvalue > $match; break;
		case ("greater|equal"): return $realvalue >= $match; break;
		default: return $realvalue == $match;
	}
	
}

?>