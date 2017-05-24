<?php

/**
 * webEdition CMS
 *
 * $Rev: 13349 $
 * $Author: mokraemer $
 * $Date: 2017-02-12 18:21:01 +0100 (So, 12. Feb 2017) $
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
abstract class we_tag_tag{
### tag utility functions ###

	public static function redirectTagOutput($returnvalue, $nameTo, $to = 'screen'){
		switch(isset($GLOBALS['calculate']) ? 'calculate' : $to){
			case 'request':
				self::setVarArray($_REQUEST, $nameTo, $returnvalue);
				return null;
			case 'post':
				self::setVarArray($_POST, $nameTo, $returnvalue);
				return null;
			case 'get':
				self::setVarArray($_GET, $nameTo, $returnvalue);
				return null;
			case 'global':
				self::setVarArray($GLOBALS, $nameTo, $returnvalue);
				return null;
			case 'session':
				self::setVarArray($_SESSION, $nameTo, $returnvalue);
				return null;
			case 'top':
				$GLOBALS['WE_MAIN_DOC_REF']->setElement($nameTo, $returnvalue);
				return null;
			case 'block' :
				$nameTo = we_tag_getPostName($nameTo);
			case 'self' :
				$GLOBALS['we_doc']->setElement($nameTo, $returnvalue);
				return null;
			case 'sessionfield' :
				if(isset($_SESSION['webuser'][$nameTo])){
					$_SESSION['webuser'][$nameTo] = $returnvalue;
				}
				return null;
			case 'calculate':
				return we_base_util::std_numberformat($returnvalue);
			case 'screen':
			default:
				return $returnvalue;
		}
		return null;
	}

	private static function setVarArray(&$arr, $string, $value){
		if(strpos($string, '[') === false){
			$arr[$string] = $value;
			return;
		}
		$current = &$arr;

		/* 	$arr_matches = [];
		  preg_match('/[^\[\]]+/', $string, $arr_matches);
		  $first = $arr_matches[0];
		  preg_match_all('/\[([^\]]*)\]/', $string, $arr_matches, PREG_PATTERN_ORDER);
		  $arr_matches = $arr_matches[1];
		  array_unshift($arr_matches, $first); */
		$arr_matches = preg_split('/\]\[|\[|\]/', $string);
		$last = count($arr_matches) - 1;
		unset($arr_matches[$last--]); //preg split has an empty element at the end
		foreach($arr_matches as $pos => $dimension){
			if(empty($dimension)){
				$dimension = count($current);
			}
			if($pos == $last){
				$current[$dimension] = $value;
				return;
			}
			if(!isset($current[$dimension])){
				$current[$dimension] = [];
			}
			$current = &$current[$dimension];
		}
	}

}
