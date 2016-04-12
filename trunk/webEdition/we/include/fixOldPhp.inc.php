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
//FIXME: remove with PHP 5.5.... but currently some hosters have this still enabled.
if(get_magic_quotes_gpc()){
	$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
	while(list($key, $val) = each($process)){
		foreach($val as $k => $v){
			unset($process[$key][$k]);
			if(is_array($v)){
				$process[$key][stripslashes($k)] = $v;
				$process[] = &$process[$key][stripslashes($k)];
			} else {
				$process[$key][stripslashes($k)] = stripslashes($v);
			}
		}
	}
	unset($process);
}

//FIMXE: remove with end of php support 5.4
if(!function_exists('boolval')){

	function boolval($val){
		return $val ? true : false;
	}

}

//FIXME: for php 5.3
if(!defined('JSON_UNESCAPED_UNICODE')){
	define('JSON_UNESCAPED_UNICODE', 0);
}