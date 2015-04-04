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
//don't start a we-session!!
if(!defined('NO_SESS')){
	define('NO_SESS', 1);
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

if((isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode'])){
	return;
}

/**
 * url without query string
 * we need this in some we_tag()
 */
define('WE_REDIRECTED_SEO', (isset($_SERVER['REDIRECT_URL']) ?
		$_SERVER['REDIRECT_URL'] :
		(isset($_SERVER['PHP_SELF']) ?
			$_SERVER['PHP_SELF'] :
			$_SERVER['SCRIPT_NAME'])
	)
);

//do we need this any more?
$prefix = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE urlMap="' . $DB_WE->escape($_SERVER["HTTP_HOST"]) . '"'); //multidomains

$urlQueryString = array();

if(isset($_SERVER['SCRIPT_URL']) && ($_SERVER['SCRIPT_URL']) && !strpos($_SERVER['SCRIPT_URL'], WEBEDITION_DIR)){
	$pathParts = pathinfo($prefix . urldecode($_SERVER['SCRIPT_URL']));
} elseif(isset($_SERVER['REDIRECT_URL']) && ($_SERVER['REDIRECT_URL']) && !strpos($_SERVER['REDIRECT_URL'], WEBEDITION_DIR)){
	$pathParts = pathinfo($prefix . urldecode($_SERVER['REDIRECT_URL']));
} elseif(isset($_SERVER['REQUEST_URI']) && ($_SERVER['REQUEST_URI']) && !strpos($_SERVER['REQUEST_URI'], WEBEDITION_DIR)){
	$splittUrl = (strpos($_SERVER['REQUEST_URI'], '?') !== false) ? explode('?', urldecode($_SERVER['REQUEST_URI'])) : urldecode($_SERVER['REQUEST_URI']);
	if(is_array($splittUrl)){
		$pathParts = pathinfo($prefix . $splittUrl[0]);
		parse_str($splittUrl[1], $urlQueryString); //get query string
	} else {
		$pathParts = pathinfo($prefix . $splittUrl);
	}
} else {
	$pathParts = array();
}

//get query string if there
if(isset($_SERVER['REDIRECT_QUERY_STRING']) && ($_SERVER['REDIRECT_QUERY_STRING'])){
	parse_str($_SERVER['REDIRECT_QUERY_STRING'], $urlQueryString);
} elseif(isset($_SERVER['QUERY_STRING']) && ($_SERVER['QUERY_STRING'])){
	parse_str($_SERVER['QUERY_STRING'], $urlQueryString);
}

/**
 * now, we looking for an object ID an starting with the last part of the URL
 * for that we split the URL via pathinfo()
 * and then we are checking the URL from right to left
 * e.g.
 * /mainfolder/subfolder/part-1-of-seo-url/part-2-of-seo-url
 *
 * first check: part-2-of-seo-url --> nothing is found
 * second check: part-1-of-seo-url/part-2-of-seo-url --> we get the object
 * and so one
 */
$urlLookingFor = '';
while(($pathParts['filename'])){
	$urlLookingFor = (URLENCODE_OBJECTSEOURLS ?
			strtr(urlencode($pathParts['filename']), array('%2F' => '/', '//' => '/')) :
			strtr($pathParts['filename'], array('//' => '/'))
		) . (($urlLookingFor) ? '/' . $urlLookingFor : '');

	if(($object = getHash('SELECT ID,TriggerID FROM ' . OBJECT_FILES_TABLE . ' WHERE Published>0 AND Url="' . $GLOBALS['DB_WE']->escape($urlLookingFor) . '" LIMIT 1'))){

		//remove all cookies from Request String if set (if not, cookies are exposed on listviews etc & max interfer with given Cookies)
		if(stristr('C', ini_get('request_order') ? : ini_get('variables_order'))){
			//unset all cookies from request
			foreach(array_keys($_COOKIE) as $name){
				unset($_REQUEST[$name]);
			}
		}

		//should we also send $_GET?
		$_GET = isset($_GET) ? array_merge($_GET, $urlQueryString) : $urlQueryString;

		$_REQUEST = isset($_REQUEST) ? array_merge($_REQUEST, $urlQueryString) : $urlQueryString;
		$_REQUEST['we_objectID'] = $object['ID'];
		$_REQUEST['we_oid'] = $object['ID'];
		$_SERVER['SCRIPT_NAME'] = id_to_path($object['TriggerID'], FILE_TABLE);

		we_html_tools::setHttpCode(200);
		include($_SERVER['DOCUMENT_ROOT'] . WEBEDITION_DIR . '../' . $_SERVER['SCRIPT_NAME']);

		exit;
	} else {//reduce the rest of the given url and try again
		$pathParts = pathinfo($pathParts['dirname']);
	}
}

/**
 * noting found show errorDoc404
 * for seo it's better to make a redirect to errorDoc404 instead of include,
 * but for that we need en separate webedition config parameter
 *
 * header("Location: http://" . $_SERVER['HTTP_HOST'] . $errorDoc404, true, (SUPPRESS404CODE ? 200 : 404));
 */
we_html_tools::setHttpCode(SUPPRESS404CODE ? 200 : 404);
$errorDoc404 = ERROR_DOCUMENT_NO_OBJECTFILE ? id_to_path(ERROR_DOCUMENT_NO_OBJECTFILE, FILE_TABLE) : 0;
if($errorDoc404){
	include($_SERVER['DOCUMENT_ROOT'] . WEBEDITION_DIR . '../' . $errorDoc404);
}

exit;
