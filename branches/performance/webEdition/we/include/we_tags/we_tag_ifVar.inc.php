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

//FIXME: check what todo with match in case of blocks!

function we_tag_ifVar($attribs, $content){
	if (($foo = attributFehltError($attribs, "name", "ifVar"))) {
		print($foo);
		return "";
	}
	if (($foo = attributFehltError($attribs, "match", "ifVar", true))) {
		print($foo);
		return "";
	}

	$match = we_getTagAttribute("match", $attribs);
	$name = we_getTagAttribute("name", $attribs);
	$type = we_getTagAttribute("type", $attribs);
	$operator  = we_getTagAttribute("operator", $attribs);

	$matchArray = makeArrayFromCSV($match);
	$_size = sizeof($matchArray);

	switch ($type) {
		case "customer" :
		case "sessionfield" :
			if ($_size==1 && $operator!=''  && isset($_SESSION["webuser"][$name]) ){
				switch ($operator) {
					case "equal": return $_SESSION["webuser"][$name] == $match; break;
					case "less": return $_SESSION["webuser"][$name] < $match; break;
					case "less|equal": return $_SESSION["webuser"][$name] <= $match; break;
					case "greater": return $_SESSION["webuser"][$name] > $match; break;
					case "greater|equal": return $_SESSION["webuser"][$name] >= $match; break;
					case "contains": if (strpos($_SESSION["webuser"][$name],$match)!== false) {return true;} else {return false;} break;
				}
			} else {
				return (isset($_SESSION["webuser"][$name]) && in_array($_SESSION["webuser"][$name], $matchArray));
			}
		case "global" :
			if ($_size==1 && $operator!='' && isset($GLOBALS[$name]) ){
				switch ($operator) {
					case "equal": return $GLOBALS[$name] == $match; break;
					case "less": return $GLOBALS[$name] < $match; break;
					case "less|equal": return $GLOBALS[$name] <= $match; break;
					case "greater": return $GLOBALS[$name] > $match; break;
					case "greater|equal": return $GLOBALS[$name] >= $match; break;
					case "contains": if (strpos($GLOBALS[$name],$match)!== false) {return true;} else {return false;} break;
				}
			} else {
				return (isset($GLOBALS[$name]) && in_array($GLOBALS[$name], $matchArray));
			}
		case "request" :
			if (isset($_REQUEST[$name])) {
				if ($_size==1 && $operator!=''  && isset($_REQUEST[$name]) ){
					switch ($operator) {
						case "equal": return $_REQUEST[$name] == $match; break;
						case "less": return $_REQUEST[$name] < $match; break;
						case "less|equal": return $_REQUEST[$name] <= $match; break;
						case "greater": return $_REQUEST[$name] > $match; break;
						case "greater|equal": return $_REQUEST[$name] >= $match; break;
						case "contains": if (strpos($_REQUEST[$name],$match)!== false) {return true;} else {return false;} break;
					}
				} else {
					return (isset($_REQUEST[$name]) && in_array($_REQUEST[$name], $matchArray));
				}
			} else {
				return "";
			}
		case "post" :
			if (isset($_POST[$name])) {
				if ($_size==1 && $operator!='' && isset($_POST[$name]) ){
					switch ($operator) {
						case "equal": return $_POST[$name] == $match; break;
						case "less": return $_POST[$name] < $match; break;
						case "less|equal": return $_POST[$name] <= $match; break;
						case "greater": return $_POST[$name] > $match; break;
						case "greater|equal": return $_POST[$name] >= $match; break;
						case "contains": if (strpos($_POST[$name],$match)!== false) {return true;} else {return false;} break;
					}
				} else {
					return (isset($_POST[$name]) && in_array($_POST[$name], $matchArray));
				}
			} else {
				return "";
			}
		case "get" :
			if (isset($_GET[$name])) {
				if ($_size==1 && $operator!='' && isset($_GET[$name]) ){
					switch ($operator) {
						case "equal": return $_GET[$name] == $match; break;
						case "less": return $_GET[$name] < $match; break;
						case "less|equal": return $_GET[$name] <= $match; break;
						case "greater": return $_GET[$name] > $match; break;
						case "greater|equal": return $_GET[$name] >= $match; break;
						case "contains": if (strpos($_GET[$name],$match)!== false) {return true;} else {return false;} break;
					}
				} else {
					return (isset($_GET[$name]) && in_array($_GET[$name], $matchArray));
				}
			} else {
				return "";
			}
		case "session" :
			if (isset($_SESSION[$name])) {
				if ($_size==1 && $operator!='' && isset($_SESSION[$name]) ){
					switch ($operator) {
						case "equal": return $_SESSION[$name] == $match; break;
						case "less": return $_SESSION[$name] < $match; break;
						case "less|equal": return $_SESSION[$name] <= $match; break;
						case "greater": return $_SESSION[$name] > $match; break;
						case "greater|equal": return $_SESSION[$name] >= $match; break;
						case "contains": if (strpos($_SESSION[$name],$match)!== false) {return true;} else {return false;} break;
					}
				} else {
					return (isset($_SESSION[$name]) && in_array($_SESSION[$name], $matchArray));
				}
			} else {
				return "";
			}
		case "property" :
			$docAttr = we_getTagAttribute("doc", $attribs);
			$doc = we_getDocForTag($docAttr, true);
			eval('$var = $doc->' . $name . ';');
			if ($_size==1 && $operator!='' && isset($var) ){
				switch ($operator) {
					case "equal": return $var == $match; break;
					case "less": return $var < $match; break;
					case "less|equal": return $var <= $match; break;
					case "greater": return $var > $match; break;
					case "greater|equal": return $var >= $match; break;
					case "contains": if (strpos($var,$match)!== false) {return true;} else {return false;} break;
				}
			} else {
				return in_array($var, $matchArray);
			}
		case "document" :
		default :
			$docAttr = we_getTagAttribute("doc", $attribs);
			$doc = we_getDocForTag($docAttr, true);
			if ($_size==1 && $operator!='' ){
				switch ($operator) {
					case "equal": return $doc->getElement($name) == $match; break;
					case "less": return $doc->getElement($name) < $match; break;
					case "less|equal": return $doc->getElement($name) <= $match; break;
					case "greater": return $doc->getElement($name) > $match; break;
					case "greater|equal": return $doc->getElement($name) >= $match; break;
					case "contains": if (strpos($doc->getElement($name),$match)!== false) {return true;} else {return false;} break;
				}
			} else {
				return in_array($doc->getElement($name), $matchArray);
			}
	}
}
