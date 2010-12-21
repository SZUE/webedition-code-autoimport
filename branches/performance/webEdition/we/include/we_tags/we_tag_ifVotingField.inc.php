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
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/voting/weVoting.php');

function we_tag_ifVotingField($attribs,$content) {
	$foo = attributFehltError($attribs, "match", "ifVotingField");
	if ($foo) {
		print($foo);
		return "";
	}

	$operator  = we_getTagAttribute("operator", $attribs);
	if ($operator == "less" || $operator == "less|equal" || $operator == "greater" || $operator == "greater|equal") {
    	$match = (int) we_getTagAttributeTagParser("match",$attribs,'',false,false,true);
	} else {
		$match = we_getTagAttributeTagParser("match",$attribs,'',false,false,true);
	}
	$atts = removeAttribs($attribs,array('match','operator'));
	if ($operator == "less" || $operator == "less|equal" || $operator == "greater" || $operator == "greater|equal") {
		$realvalue = (int) we_tag('votingField',$atts, "");
	} else {
		$realvalue = we_tag('votingField',$atts, "");
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
