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
	if(($foo = attributFehltError($attribs, array('name' => false, 'match' => true), __FUNCTION__))){
		print($foo);
		return false;
	}

	$match = weTag_getAttribute('match', $attribs);
	$type = weTag_getAttribute('type', $attribs);
	$operator = weTag_getAttribute('operator', $attribs, 'equal');

	$matchArray = makeArrayFromCSV($match);
	$size = count($matchArray);

	switch($type){
		case 'customer' :
		case 'sessionfield' :
			$name = weTag_getAttribute('_name_orig', $attribs);

			return ($size == 1 && $operator != '' && isset($_SESSION['webuser'][$name]) ?
					_we_tag_ifVar_op($operator, $_SESSION['webuser'][$name], $match) :
					(isset($_SESSION['webuser'][$name]) && in_array($_SESSION['webuser'][$name], $matchArray)));

		case 'global' :
			$name = weTag_getAttribute('_name_orig', $attribs);
			//$name = isset($GLOBALS[$name]) ? $name : (isset($GLOBALS[$name_orig]) ? $name_orig : $name);
			$var = getArrayValue($GLOBALS, null, $name);
			return ($size == 1 && $operator != '' ?
					_we_tag_ifVar_op($operator, $var, $match) :
					(!empty($var) && in_array($var, $matchArray)));

		case 'request' :
			$name = weTag_getAttribute('_name_orig', $attribs);
			if(isset($_REQUEST[$name])){
				return ($size == 1 && $operator != '' && isset($_REQUEST[$name]) ?
						_we_tag_ifVar_op($operator, $_REQUEST[$name], $match) :
						(isset($_REQUEST[$name]) && in_array($_REQUEST[$name], $matchArray)));
			}
			return false;

		case 'post' :
			$name = weTag_getAttribute('_name_orig', $attribs);
			if(isset($_POST[$name])){
				return ($size == 1 && $operator != '' && isset($_POST[$name]) ?
						_we_tag_ifVar_op($operator, $_POST[$name], $match) :
						(isset($_POST[$name]) && in_array($_POST[$name], $matchArray)));
			}
			return false;

		case 'get' :
			$name = weTag_getAttribute('_name_orig', $attribs);
			if(isset($_GET[$name])){
				return ($size == 1 && $operator != '' && isset($_GET[$name]) ?
						_we_tag_ifVar_op($operator, $_GET[$name], $match) :
						(isset($_GET[$name]) && in_array($_GET[$name], $matchArray)));
			}
			return false;

		case 'session' :
			$name = weTag_getAttribute('_name_orig', $attribs);
			if(isset($_SESSION[$name])){
				return ($size == 1 && $operator != '' && isset($_SESSION[$name]) ?
						_we_tag_ifVar_op($operator, $_SESSION[$name], $match) :
						(isset($_SESSION[$name]) && in_array($_SESSION[$name], $matchArray)));
			} else {
				return false;
			}
		case 'property' :
			$name = weTag_getAttribute('_name_orig', $attribs);
			$docAttr = weTag_getAttribute('doc', $attribs);
			$doc = we_getDocForTag($docAttr, true);
			$var = $doc->$name;
			return ($size == 1 && $operator != '' && isset($var) ?
					_we_tag_ifVar_op($operator, $var, $match) :
					in_array($var, $matchArray));

		case 'document' :
		default :
			$docAttr = weTag_getAttribute('doc', $attribs);
			$doc = we_getDocForTag($docAttr, true);
			$val = $doc->getField($attribs, $type, true);

			return ($size == 1 && $operator != '' ?
					_we_tag_ifVar_op($operator, $val, $match) :
					in_array($val, $matchArray));
	}
}