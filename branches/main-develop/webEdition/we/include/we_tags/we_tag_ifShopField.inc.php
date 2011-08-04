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

function we_tag_ifShopField($attribs,$content) {
	$foo = attributFehltError($attribs, "name", "ifShopField");if($foo) return $foo;
	$foo = attributFehltError($attribs, "reference", "ifShopField");if($foo) return $foo;
	$foo = attributFehltError($attribs, "shopname", "ifShopField");if($foo) return $foo;
	$foo = attributFehltError($attribs, "match", "ifShopField", true);if($foo) return $foo;

	$match = we_getTagAttribute("match", $attribs);

	$name      = we_getTagAttribute("name", $attribs);
	$reference = we_getTagAttribute("reference", $attribs);
	$shopname  = we_getTagAttribute("shopname", $attribs);
	$operator  = we_getTagAttribute("operator", $attribs);

	if ($operator == "less" || $operator == "less|equal" || $operator == "greater" || $operator == "greater|equal") {
		$match = (int) $match;
	}
	$attribs['type']='print';
	$atts = removeAttribs($attribs,array('match','operator'));
	if ($operator == "less" || $operator == "less|equal" || $operator == "greater" || $operator == "greater|equal") {
		$realvalue = (int) we_tag('shopField',$atts, "");
	}else {
		$realvalue = we_tag('shopField',$atts, "");
	}

	switch ($operator) {
		case "equal": return $realvalue == $match; break;
		case "less": return $realvalue < $match; break;
		case "less|equal": return $realvalue <= $match; break;
		case "greater": return $realvalue > $match; break;
		case "greater|equal": return $realvalue >= $match; break;
		case "contains": if (strpos($realvalue,$match)!== false) {return true;} else {return false;} break;
		default: return $realvalue == $match;
	}
}
