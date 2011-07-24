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

/**
 *
 * @param mixed $_position position-value of position-Array (first,last,even,odd,#)
 * @param int $_size size of position Array
 * @param string $operator operator (equal,less,greater,less|equal,greater|equal)
 * @param int $position position of comparable
 * @param int $size size of comparable
 * @return mixed (true,false,-1) -1 if no decission is made yet - pass next element of position array 
 */
function _we_tag_ifPosition_op($_position, $_size, $operator, $position, $size) {
	switch ($_position) {
		case "first" :
			if ($_size == 1 && $operator != '') {
				switch ($operator) {
					case "equal": return $position == 1;
					case "less": return $position < 1;
					case "less|equal": return $position <= 1;
					case "greater": return $position > 1;
					case "greater|equal": return $position >= 1;
				}
			} else {
				if ($position == 1) {
					return true;
				}
			}
			break;
		case "last" :
			if ($_size == 1 && $operator != '') {
				switch ($operator) {
					case "equal": return $position == $size;
					case "less": return $position < $size;
					case "less|equal": return $position <= $size;
					case "greater|equal": return $position >= $size;
				}
			} else {
				if ($position == $size) {
					return true;
				}
			}
			break;
		case "odd" :
			if ($position % 2 != 0) {
				return true;
			}
			break;
		case "even" :
			if ($position % 2 == 0) {
				return true;
			}
			break;

		default :
			$_position = intval($_position); // Umwandeln in integer
			if ($_size == 1 && $operator != '') {
				switch ($operator) {
					case "equal": return $position == $_position;
					case "less": return $position < $_position;
					case "less|equal": return $position <= $_position;
					case "greater": return $position > $_position;
					case "greater|equal": return $position >= $_position;
				}
			} else {
				if ($position == $_position) {
					return true;
				}
			}
			break;
	}
	//no decission yet
	return -1;
}

function we_tag_ifPosition($attribs, $content){
	global $lv;
	//	content is not needed in this tag


	$missingAttrib = attributFehltError($attribs, "type", "ifPosition") 
					|| attributFehltError($attribs, "position", "ifPosition");

	if ($missingAttrib) {
		print $missingAttrib;
		return "";
	}


	$type = we_getTagAttribute("type", $attribs);
	$position = we_getTagAttribute("position", $attribs);
	$positionArray = explode(',', $position);
	$_size = sizeof($positionArray);
	$operator  = we_getTagAttribute("operator", $attribs);

	switch ($type) {
		case "listview" : //	inside a listview, we take direct global listview object
			foreach ($positionArray as $_position) {
				$tmp=_we_tag_ifPosition_op($_position,$_size,$operator,$lv->count,$lv->anz);
				if($tmp!==-1){
					return $tmp;
				}
			}
			break;

		case "linklist" : //	look in fkt we_tag_linklist and callss we_linklist for details
			//	first we must get right array !!!
			$missingAttrib = attributFehltError($attribs, "reference", "ifPosition");
			if ($missingAttrib) {
				print $missingAttrib;
				return "";
			}
			$_reference = we_getTagAttribute("reference", $attribs);

			foreach ($GLOBALS['we_position']['linklist'] as $name => $arr) {

				if (strpos($name, $_reference) === 0) {
					if (is_array($arr)) {
						$_content = $arr;
					}
				}
			}

			if (isset($_content) && $_content['position']) {
				foreach ($positionArray as $_position) {
					$tmp=_we_tag_ifPosition_op($_position,$_size,$operator,$_content['position'],$_content['size']);
					if($tmp!==-1){
						return $tmp;
					}
				}
			}
			break;

		case "block" : //	look in function we_tag_block for details
			$missingAttrib = attributFehltError($attribs, "reference", "ifPosition");
			if ($missingAttrib) {
				print $missingAttrib;
				return "";
			}

			$_reference = we_getTagAttribute("reference", $attribs);

			foreach ($GLOBALS['we_position']['block'] as $name => $arr) {
				if (strpos($name, $_reference) === 0) {
					$_content = $arr;
				}
			}

			if (isset($_content) && $_content['position']) {
				foreach ($positionArray as $_position) {
					$tmp=_we_tag_ifPosition_op($_position,$_size,$operator,$_content['position'],$_content['size']);
					if($tmp!==-1){
						return $tmp;
					}
				}
			}
			break;

		case "listdir" : //	inside a listview
			if (isset($GLOBALS['we_position']['listdir'])) {
				$_content = $GLOBALS['we_position']['listdir'];
			}
			if (isset($_content) && $_content['position']) {
				foreach ($positionArray as $_position) {
					$tmp=_we_tag_ifPosition_op($_position,$_size,$operator,$_content['position'],$_content['size']);
					if($tmp!==-1){
						return $tmp;
					}
				}
			}
			break;
		default :
			return false;
	}
	return false;
}
