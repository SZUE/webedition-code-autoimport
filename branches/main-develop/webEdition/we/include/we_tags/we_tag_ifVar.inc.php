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
		case 'isin':
			return (strpos($match, $first) !== false);
	}
}

function we_tag_ifVar(array $attribs){
	if(($foo = attributFehltError($attribs, ['name' => false, 'match' => true], __FUNCTION__))){
		echo $foo;
		return false;
	}

	$match = weTag_getAttribute('match', $attribs, '', we_base_request::RAW);
	$type = weTag_getAttribute('type', $attribs, '', we_base_request::STRING);
	$operator = weTag_getAttribute('operator', $attribs, 'equal', we_base_request::STRING);

	if(is_bool($match)){
		$size = 1;
	} else {
		$matchArray = makeArrayFromCSV($match);
		$size = count($matchArray);
	}

	$nameOrig = weTag_getAttribute('_name_orig', $attribs, '', we_base_request::STRING);

	switch($type){
		case 'customer' :
		case 'sessionfield' :
			return ($size == 1 && $operator != '' && isset($_SESSION['webuser'][$nameOrig]) ?
							_we_tag_ifVar_op($operator, $_SESSION['webuser'][$nameOrig], $match) :
							(isset($_SESSION['webuser'][$nameOrig]) && in_array($_SESSION['webuser'][$nameOrig], $matchArray)));

		case 'global':
			//$name = isset($GLOBALS[$name]) ? $name : (isset($GLOBALS[$name_orig]) ? $name_orig : $name);
			$var = getArrayValue($GLOBALS, null, $nameOrig);
			return ($size == 1 && $operator ?
							_we_tag_ifVar_op($operator, $var, $match) :
							(!empty($var) && in_array($var, $matchArray)));

		case 'request':
			if(!isset($_REQUEST[$nameOrig])){
				return false;
			}
			return ($size == 1 && $operator ?
							_we_tag_ifVar_op($operator, $_REQUEST[$nameOrig], $match) :
							( in_array($_REQUEST[$nameOrig], $matchArray)));

		case 'post':
			if(!isset($_POST[$nameOrig])){
				return false;
			}
			return ($size == 1 && $operator ?
							_we_tag_ifVar_op($operator, $_POST[$nameOrig], $match) :
							( in_array($_POST[$nameOrig], $matchArray)));

		case 'get':
			if(!isset($_GET[$nameOrig])){
				return false;
			}
			return ($size == 1 && $operator ?
							_we_tag_ifVar_op($operator, $_GET[$nameOrig], $match) :
							(in_array($_GET[$nameOrig], $matchArray)));

		case 'session':
			if(!isset($_SESSION[$nameOrig])){
				return false;
			}
			return ($size == 1 && $operator ?
							_we_tag_ifVar_op($operator, $_SESSION[$nameOrig], $match) :
							(in_array($_SESSION[$nameOrig], $matchArray)));

		case 'property':
			$doc = we_getDocForTag(weTag_getAttribute('doc', $attribs, '', we_base_request::STRING), true);
			$var = $doc->$nameOrig;
			return ($size == 1 && $operator != '' && isset($var) ?
							_we_tag_ifVar_op($operator, $var, $match) :
							in_array($var, $matchArray));

		case 'document':
		default:
			$doc = we_getDocForTag(weTag_getAttribute('doc', $attribs, '', we_base_request::STRING), true);
			$val = $doc->getField($attribs, $type, true);

			return ($size == 1 && $operator ?
							_we_tag_ifVar_op($operator, $val, $match) :
							in_array($val, $matchArray));
	}
}
