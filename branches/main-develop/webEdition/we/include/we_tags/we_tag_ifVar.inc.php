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
function _we_tag_ifVar_op($operator, $first, $match){
	switch($operator){
		default:
		case 'equal':
			return $first == $match;
		case 'less':
			return $first < $match;
		case 'less|equal':
			return $first <= $match;
		case 'greater':
			return $first > $match;
		case 'greater|equal':
			return $first >= $match;
		case 'contains':
			return (strpos($first, $match) !== false);
	}
}

function we_tag_ifVar($attribs){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		print($foo);
		return false;
	}
	if(($foo = attributFehltError($attribs, 'match', __FUNCTION__, true))){
		print($foo);
		return false;
	}

	$match = weTag_getAttribute('match', $attribs);
	$type = weTag_getAttribute('type', $attribs);
	$operator = weTag_getAttribute('operator', $attribs, 'equal');

	$matchArray = makeArrayFromCSV($match);
	$_size = count($matchArray);

	switch($type){
		case 'customer' :
		case 'sessionfield' :
			$name = weTag_getAttribute('_name_orig', $attribs);

			if($_size == 1 && $operator != '' && isset($_SESSION['webuser'][$name])){
				return _we_tag_ifVar_op($operator, $_SESSION['webuser'][$name], $match);
			} else{
				return (isset($_SESSION['webuser'][$name]) && in_array($_SESSION['webuser'][$name], $matchArray));
			}
		case 'global' :
			$name = weTag_getAttribute('name', $attribs);
			$name_orig = weTag_getAttribute('_name_orig', $attribs);
			$name = isset($GLOBALS[$name]) ? $name : (isset($GLOBALS[$name_orig]) ? $name_orig : $name);

			if($_size == 1 && $operator != '' && isset($GLOBALS[$name])){
				return _we_tag_ifVar_op($operator, $GLOBALS[$name], $match);
			} else{
				return (isset($GLOBALS[$name]) && in_array($GLOBALS[$name], $matchArray));
			}
		case 'request' :
			$name = weTag_getAttribute('_name_orig', $attribs);
			if(isset($_REQUEST[$name])){
				if($_size == 1 && $operator != '' && isset($_REQUEST[$name])){
					return _we_tag_ifVar_op($operator, $_REQUEST[$name], $match);
				} else{
					return (isset($_REQUEST[$name]) && in_array($_REQUEST[$name], $matchArray));
				}
			} else{
				return false;
			}
		case 'post' :
			$name = weTag_getAttribute('_name_orig', $attribs);
			if(isset($_POST[$name])){
				if($_size == 1 && $operator != '' && isset($_POST[$name])){
					return _we_tag_ifVar_op($operator, $_POST[$name], $match);
				} else{
					return (isset($_POST[$name]) && in_array($_POST[$name], $matchArray));
				}
			} else{
				return false;
			}
		case 'get' :
			$name = weTag_getAttribute('_name_orig', $attribs);
			if(isset($_GET[$name])){
				if($_size == 1 && $operator != '' && isset($_GET[$name])){
					return _we_tag_ifVar_op($operator, $_GET[$name], $match);
				} else{
					return (isset($_GET[$name]) && in_array($_GET[$name], $matchArray));
				}
			} else{
				return false;
			}
		case 'session' :
			$name = weTag_getAttribute('_name_orig', $attribs);
			if(isset($_SESSION[$name])){
				if($_size == 1 && $operator != '' && isset($_SESSION[$name])){
					return _we_tag_ifVar_op($operator, $_SESSION[$name], $match);
				} else{
					return (isset($_SESSION[$name]) && in_array($_SESSION[$name], $matchArray));
				}
			} else{
				return false;
			}
		case 'property' :
			$name = weTag_getAttribute('_name_orig', $attribs);
			$docAttr = weTag_getAttribute('doc', $attribs);
			$doc = we_getDocForTag($docAttr, true);
			$var = $doc->$name;
			if($_size == 1 && $operator != '' && isset($var)){
				return _we_tag_ifVar_op($operator, $var, $match);
			} else{
				return in_array($var, $matchArray);
			}
		case 'document' :
		default :
			$docAttr = weTag_getAttribute('doc', $attribs);
			$doc = we_getDocForTag($docAttr, true);
			$val = $doc->getField($attribs, $type, true);

			if($_size == 1 && $operator != ''){
				return _we_tag_ifVar_op($operator, $val, $match);
			} else{
				return in_array($val, $matchArray);
			}
	}
}
