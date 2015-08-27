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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
//don't start a we-session!!
if(!defined('NO_SESS')){
	define('NO_SESS', 1);
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$myRequest = array();
if(isset($_SERVER['REDIRECT_QUERY_STRING']) && $_SERVER['REDIRECT_QUERY_STRING'] != ''){
	parse_str($_SERVER['REDIRECT_QUERY_STRING'], $myRequest);
} elseif(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != '' && strpos($_SERVER['REQUEST_URI'], '?') !== false){
	$zw = explode('?', $_SERVER['REQUEST_URI']);
	parse_str($zw[1], $myRequest);
}

define('WE_REDIRECTED_SEO', (isset($_SERVER['REDIRECT_URL']) ?
				$_SERVER['REDIRECT_URL'] :
				(isset($_SERVER['PHP_SELF']) ?
						$_SERVER['PHP_SELF'] :
						$_SERVER['SCRIPT_NAME'])
		)
); //url without query string
// get attributes
$error404doc = (ERROR_DOCUMENT_NO_OBJECTFILE ? : 0);

$hiddendirindex = false;
$dirindexarray = array();
if(NAVIGATION_DIRECTORYINDEX_NAMES && ( NAVIGATION_DIRECTORYINDEX_HIDE || WYSIWYGLINKS_DIRECTORYINDEX_HIDE || TAGLINKS_DIRECTORYINDEX_HIDE )){
	$dirindexarray = array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES));
	$hiddendirindex = true;
}
$prefix = (isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]) ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE urlMap="' . $DB_WE->escape($_SERVER["HTTP_HOST"]) . '"') : '';

$path_parts = array();
if(isset($_SERVER['SCRIPT_URL']) && $_SERVER['SCRIPT_URL'] != ''){
	$path_parts = pathinfo($prefix . urldecode($_SERVER['SCRIPT_URL']));
} elseif(isset($_SERVER['REDIRECT_URL']) && $_SERVER['REDIRECT_URL'] && $_SERVER['REDIRECT_URL'] != WEBEDITION_DIR . 'redirectSEOurls.php'){
	$path_parts = pathinfo($prefix . urldecode($_SERVER['REDIRECT_URL']));
} elseif(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']){
	if(strpos($_SERVER['REQUEST_URI'], '?') !== false){
		$zw2 = explode('?', urldecode($_SERVER['REQUEST_URI']));
		$path_parts = pathinfo($prefix . $zw2[0]);
	} else {
		$path_parts = pathinfo($prefix . urldecode($_SERVER['REQUEST_URI']));
	}
}

if((isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode'])){
	return;
}
$db = $GLOBALS['DB_WE'];
$displayid = $objectid = 0;
$searchfor = '';
$notfound = true;

while($notfound && isset($path_parts['dirname']) && $path_parts['dirname'] != '/' && $path_parts['dirname'] != '\\'){
	$display = $path_parts['dirname'] . DEFAULT_DYNAMIC_EXT;
	$displayid = intval(f('SELECT ID FROM ' . FILE_TABLE . ' WHERE Published>0 AND Path="' . $db->escape($display) . '" LIMIT 1'));
	$searchfor = $path_parts['basename'] . ($searchfor ? '/' . $searchfor : '');
	if(!$displayid && $hiddendirindex && $dirindexarray){
		//z79
		$display = "";

		foreach($dirindexarray as $dirindex){
			$displaytest = $path_parts['dirname'] . '/' . $dirindex;
			$displayidtest = intval(f('SELECT ID FROM ' . FILE_TABLE . ' WHERE Published>0 AND Path="' . $db->escape($displaytest) . '" LIMIT 1'));
			if($displayidtest){
				$displayid = $displayidtest;
				$display = $displaytest; //nur, wenn Datei vorhanden
				break; //wenn gefunden, kann man sich die weiteren Schleifen sparen.
			}
		}
	}
	if($displayid){
		$searchforInternal = (URLENCODE_OBJECTSEOURLS ?
						strtr(urlencode($searchfor), array('%2F' => '/', '//' => '/')) :
						strtr($searchfor, array('//' => '/'))
				);

		$objectid = intval(f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Published>0 AND Url="' . $db->escape($searchforInternal) . '" LIMIT 1'));
		if($objectid){
			$notfound = false;
			break;
		}
	}
	$path_parts = pathinfo($path_parts['dirname']);
}

if($notfound && isset($path_parts['dirname']) && $path_parts['dirname'] === '/' && $hiddendirindex){
	$searchfor = $path_parts['basename'] . ($searchfor ? '/' . $searchfor : '');

	//z109
	$display = '';
	foreach($dirindexarray as $dirindex){
		$displaytest = $path_parts['dirname'] . $dirindex;
		$displayidtest = intval(f('SELECT ID FROM ' . FILE_TABLE . ' WHERE Published>0 AND Path="' . $db->escape($displaytest) . '" LIMIT 1'));
		if($displayidtest){
			$displayid = $displayidtest;
			$display = $displaytest; //nur, wenn Datei vorhanden
			break; //wenn gefunden, kann man sich die weiteren Schleifen sparen
		}
	}
	if($displayid){
		$searchforInternal = (URLENCODE_OBJECTSEOURLS ?
						strtr(urlencode($searchfor), array('%2F' => '/')) :
						$searchfor
				);

		$objectid = intval(f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Published>0 AND Url="' . $db->escape($searchforInternal) . '" LIMIT 1'));
		if($objectid){
			$notfound = false;
		}
	}
}
if(!$notfound){
	$ro = ini_get('request_order');
	if(stripos('C', $ro ? : ini_get('variables_order'))){
		//unset all cookies from request
		foreach(array_keys($_COOKIE) as $name){
			unset($_REQUEST[$name]);
		}
	}
	$_REQUEST = array_merge($_REQUEST, $myRequest);
	$_REQUEST['we_objectID'] = $objectid;
	$_REQUEST['we_oid'] = $objectid;
	$_GET = array_merge($_GET, $myRequest);
	$_SERVER['SCRIPT_NAME'] = $display;
	we_html_tools::setHttpCode(200);

	include(WEBEDITION_PATH . '../' . $display);

	exit;
} elseif($error404doc){
	we_html_tools::setHttpCode(SUPPRESS404CODE ? 200 : 404);
	if(($doc = id_to_path($error404doc, FILE_TABLE))){
		include(WEBEDITION_PATH . '../' . $doc);
	}
	exit;
}
