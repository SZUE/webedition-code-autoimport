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

function we_tag_ifPosition($attribs, $content){
	global $lv;
	//	content is not needed in this tag


	$missingAttrib = attributFehltError($attribs, "type", "ifPosition");
	if ($missingAttrib) {
		print $missingAttrib;
		return "";
	}

	$missingAttrib = attributFehltError($attribs, "position", "ifPosition");
	if ($missingAttrib) {
		print $missingAttrib;
		return "";
	}

	$type = we_getTagAttribute("type", $attribs);
	$position = we_getTagAttribute("position", $attribs);

	$positionArray = explode(',', $position);

	$_size = sizeof($positionArray);

	$operator  = we_getTagAttribute("operator", $attribs);


	for ($i = 0; $i < $_size; $i++) {

		$_position = $positionArray[$i];

		switch ($type) {

			case "listview" : //	inside a listview, we take direct global listview object


				switch ($_position) {
					case "first" :
						if ($_size==1 && $operator!=''){
							switch ($operator) {
								case "equal": return $lv->count == 1; break;
								case "less": return $lv->count < 1; break;
								case "less|equal": return $lv->count <= 1; break;
								case "greater": return $lv->count > 1; break;
								case "greater|equal": return $lv->count >= 1; break;
							}
						} else {
							if ($lv->count == 1) {
								return true;
							}
						}
						break;
					case "last" :
						if ($_size==1 && $operator!=''){
							switch ($operator) {
								case "equal": return $lv->count == $lv->anz; break;
								case "less": return $lv->count < $lv->anz; break;
								case "less|equal": return $lv->count <= $lv->anz; break;
								case "greater|equal": return $lv->count >= $lv->anz; break;
							}
						} else {
							if ($lv->count == $lv->anz) {
								return true;
							}
						}
						break;
					case "odd" :
						if ($lv->count % 2 != 0) {
							return true;
						}
						break;
					case "even" :
						if ($lv->count % 2 == 0) {
							return true;
						}
						break;

					default :
						$_position += 0; // Umwandeln in integer
						if ($_size==1 && $operator!=''){
							switch ($operator) {
								case "equal": return $lv->count == $_position; break;
								case "less": return $lv->count < $_position; break;
								case "less|equal": return $lv->count <= $_position; break;
								case "greater": return $lv->count > $_position; break;
								case "greater|equal": return $lv->count >= $_position; break;
							}
						} else {
							if ($lv->count == $_position) {
								return true;
							}
						}
						break;
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

					switch ($_position) {
						//	$_content is the actual listview object !!!!!!
						case "first" :
							if ($_size==1 && $operator!=''){
								switch ($operator) {
									case "equal": return $_content['position'] == 1; break;
									case "less|equal": return $_content['position'] <= 1; break;
									case "greater": return $_content['position'] > 1; break;
									case "greater|equal": return $_content['position'] >= 1; break;
								}
							} else {
								if ($_content['position'] == 1) {
									return true;
								}
							}
							break;

						case "last" :
							if ($_size==1 && $operator!=''){
								switch ($operator) {
									case "equal": return $_content['position'] == $_content['size']; break;
									case "less": return $_content['position'] < $_content['size']; break;
									case "less|equal": return $_content['position'] <= $_content['size']; break;
									case "greater|equal": return $_content['position'] >= $_content['size']; break;
								}
							} else {
								if ($_content['position'] == $_content['size']) {
									return true;
								}
							}
							break;

						case "odd" :
							if ($_content['position'] % 2 != 0) {
								return true;
							}
							break;

						case "even" :
							if ($_content['position'] % 2 == 0) {
								return true;
							}
							break;

						default :
							$_position += 0; // Umwandeln in integer
							if ($_size==1 && $operator!=''){
								switch ($operator) {
									case "equal": return $_content['position'] == $_position; break;
									case "less": return $_content['position'] < $_position; break;
									case "less|equal": return $_content['position'] <= $_position; break;
									case "greater": return $_content['position'] > $_position; break;
									case "greater|equal": return $_content['position'] >= $_position; break;
								}
							} else {
								if ($_content['position'] == $_position) {
									return true;
								}
							}
							break;
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

					switch ($_position) {
						//	$_content is an array containing position and size !!!!!!
						case "first" :
							if ($_size==1 && $operator!=''){
								switch ($operator) {
									case "equal": return $_content['position'] == 1; break;
									case "less|equal": return $_content['position'] <= 1; break;
									case "greater": return $_content['position'] > 1; break;
									case "greater|equal": return $_content['position'] >= 1; break;
								}
							} else {
								if ($_content["position"] == 1) {
									return true;
								}
							}
							break;

						case "last" :
							if ($_size==1 && $operator!=''){
								switch ($operator) {
									case "equal": return $_content['position'] == $_content["size"]; break;
									case "less": return $_content['position'] < $_content["size"]; break;
									case "less|equal": return $_content['position'] <= $_content["size"]; break;
									case "greater|equal": return $_content['position'] >= $_content["size"]; break;
								}
							} else {
								if ($_content["position"] == $_content["size"]) {
									return true;
								}
							}
							break;

						case "odd" :
							if ($_content["position"] % 2 != 0) {
								return true;
							}
							break;

						case "even" :
							if ($_content["position"] % 2 == 0) {
								return true;
							}
							break;

						default :
							$_position += 0; // Umwandeln in integer
							if ($_size==1 && $operator!=''){
								switch ($operator) {
									case "equal": return $_content['position'] == $_position; break;
									case "less": return $_content['position'] < $_position; break;
									case "less|equal": return $_content['position'] <= $_position; break;
									case "greater": return $_content['position'] > $_position; break;
									case "greater|equal": return $_content['position'] >= $$_position; break;
								}
							} else {
								if ($_content["position"] == $_position) {
									return true;
								}
							}
							break;
					}
				}
				break;
			case "listdir" : //	inside a listview


				if (isset($GLOBALS['we_position']['listdir'])) {
					$_content = $GLOBALS['we_position']['listdir'];
				}
				if (isset($_content) && $_content['position']) {
					switch ($_position) {
						case "first" :
							if ($_size==1 && $operator!=''){
								switch ($operator) {
									case "equal": return $_content['position'] == 1; break;
									case "less|equal": return $_content['position'] <= 1; break;
									case "greater": return $_content['position'] > 1; break;
									case "greater|equal": return $_content['position'] >= 1; break;
								}
							} else {
								if ($_content["position"] == 1) {
									return true;
								}
							}
							break;

						case "last" :
							if ($_size==1 && $operator!=''){
								switch ($operator) {
									case "equal": return $_content['position'] == $_content["size"]; break;
									case "less": return $_content['position'] < $_content["size"]; break;
									case "less|equal": return $_content['position'] <= $_content["size"]; break;
									case "greater|equal": return $_content['position'] >= $_content["size"]; break;
								}
							} else {
								if ($_content["position"] == $_content["size"]) {
									return true;
								}
							}
							break;

						case "odd" :
							if ($_content["position"] % 2 != 0) {
								return true;
							}
							break;

						case "even" :
							if ($_content["position"] % 2 == 0) {
								return true;
							}
							break;

						default :
							$_position += 0; // Umwandeln in integer
							if ($_size==1 && $operator!=''){
								switch ($operator) {
									case "equal": return $_content['position'] == $_position; break;
									case "less": return $_content['position'] < $_position; break;
									case "less|equal": return $_content['position'] <= $_position; break;
									case "greater": return $_content['position'] > $_position; break;
									case "greater|equal": return $_content['position'] >= $$_position; break;
								}
							} else {
								if ($_content["position"] == $_position) {
									return true;
								}
							}
							break;
					}
				}
				break;

			default :
				return false;
				break;
		}
	}
	return false;
}
