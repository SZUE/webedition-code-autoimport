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


function we_tag_ifField($attribs, $content){
	$foo = attributFehltError($attribs, "name", "ifField");
	if ($foo) {
		print($foo);
		return "";
	}
	$foo = attributFehltError($attribs, "match", "ifField", true);
	if ($foo) {
		print($foo);
		return "";
	}
	$foo = attributFehltError($attribs, "type", "ifField", true);
	if ($foo) {
		print($foo);
		return "";
	}

	$match = we_getTagAttribute("match", $attribs);
	// quickfix 4192
	if (isset($GLOBALS["lv"]->BlockInside) && !$GLOBALS["lv"]->BlockInside  ){ // if due to bug 4635
		$matchA = explode("blk_",$match);
		$match = $matchA[0];
	}
	// quickfix 4192
	$matchArray = makeArrayFromCSV($match);

	$operator  = we_getTagAttribute("operator", $attribs);

	if ($operator == "less" || $operator == "less|equal" || $operator == "greater" || $operator == "greater|equal") {
    	$match = (int) $match;
	}

	//Bug #4815
	if($attribs["type"]=='float' || $attribs["type"]=='int'){$attribs["type"]='text';}

	if ($operator == "less" || $operator == "less|equal" || $operator == "greater" || $operator == "greater|equal") {
		$realvalue = (int) we_tag('field',$attribs, "");;
	}else {
		$realvalue = we_tag('field',$attribs, "");;
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
