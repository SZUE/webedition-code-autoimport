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

$urlLookingFor = (isset($_SERVER['REDIRECT_URL']) && $_SERVER['REDIRECT_URL'] && !strpos($_SERVER['REDIRECT_URL'], ltrim(WEBEDITION_DIR, "/"))) ?
	urldecode($_SERVER['REDIRECT_URL']) :
	(isset($_SERVER['REQUEST_URI']) && ($_SERVER['REQUEST_URI']) && !strpos($_SERVER['REQUEST_URI'], ltrim(WEBEDITION_DIR, "/")) ?
		parse_url(urldecode($_SERVER['REQUEST_URI']),PHP_URL_PATH) : 
		'');

if(!$urlLookingFor){
	return;
}

/**
 * url without query string
 * we need this in some we_tag()
 */
define('WE_REDIRECTED_SEO', $urlLookingFor);

/**
 * now, we looking for an object ID an starting with full path of the URL
 * and then we are checking the URL from left to right
 * e.g. /mainfolder/subfolder/part-1-of-seo-url/part-2-of-seo-url
 * in /mainfolder/subfolder is a dynamic webEdition document aka Trigger document
 * this Trigger document is part oh the whole URL but not of the object SEO-URL
 *
 * first check: mainfolder/subfolder/part-1-of-seo-url/part-2-of-seo-url --> nothing is found
 * second check: subfolder/part-1-of-seo-url/part-2-of-seo-url --> nothing is found
 * third check: part-1-of-seo-url/part-2-of-seo-url --> we get the object
 * and so one
 */

$urlLookingFor = (URLENCODE_OBJECTSEOURLS ?
	strtr(urlencode($urlLookingFor), array('%2F' => '/', '//' => '/')) :
	strtr($urlLookingFor, array('//' => '/'))
	);
 
while($urlLookingFor){
	if(($object = getHash('SELECT ID,TriggerID FROM ' . OBJECT_FILES_TABLE . ' WHERE Published>0 AND Url LIKE "' . $GLOBALS['DB_WE']->escape($urlLookingFor) . '" LIMIT 1'))){

		//remove all cookies from Request String if set (if not, cookies are exposed on listviews etc & max interfer with given Cookies)
		if(stristr('C', ini_get('request_order') ? : ini_get('variables_order'))){
			//unset all cookies from request
			foreach(array_keys($_COOKIE) as $name){
				unset($_REQUEST[$name]);
			}
		}

		//get query string if there
		$urlQueryString = array();
		if(isset($_SERVER['REDIRECT_QUERY_STRING']) && ($_SERVER['REDIRECT_QUERY_STRING'])){
			parse_str($_SERVER['REDIRECT_QUERY_STRING'], $urlQueryString);
		} elseif(isset($_SERVER['QUERY_STRING']) && ($_SERVER['QUERY_STRING'])){
			parse_str($_SERVER['QUERY_STRING'], $urlQueryString);
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
		$urlLookingFor = ltrim(stristr($urlLookingFor, "/"), "/");
	}
}

/**
 * noting found show errorDoc404
 * for seo it's better to make a redirect to errorDoc404 instead of include,
 * but for that we need en separate webedition config parameter
 *
 * header("Location: //" . $_SERVER['HTTP_HOST'] . $errorDoc404, true, (SUPPRESS404CODE ? 200 : 404));
 */
we_html_tools::setHttpCode(SUPPRESS404CODE ? 200 : 404);
$errorDoc404 = ERROR_DOCUMENT_NO_OBJECTFILE ? id_to_path(ERROR_DOCUMENT_NO_OBJECTFILE, FILE_TABLE) : 0;
if($errorDoc404){
	include($_SERVER['DOCUMENT_ROOT'] . WEBEDITION_DIR . '../' . $errorDoc404);
}

exit;
