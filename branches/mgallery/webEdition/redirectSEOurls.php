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
//don't start a we-session!
if(!defined('NO_SESS')){
	define('NO_SESS', 1);
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

if(!empty($GLOBALS['we_editmode'])){
	return;
}

$urlLookingFor = (!empty($_SERVER['REDIRECT_URL']) && !strpos($_SERVER['REDIRECT_URL'], ltrim(WEBEDITION_DIR, "/"))) ?
	urldecode($_SERVER['REDIRECT_URL']) :
	(!empty($_SERVER['REQUEST_URI']) && !strpos($_SERVER['REQUEST_URI'], ltrim(WEBEDITION_DIR, "/")) ?
		parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH) :
		'');

if(!$urlLookingFor){
	return;
}

/**
 * url without query string
 * we need this in some we_tag() and to avoid dublicate content
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

while($urlLookingFor){// first we try to get the object
	if(($object = getHash('SELECT ID,ParentID,TriggerID,Url,Workspaces,ExtraWorkspacesSelected FROM ' . OBJECT_FILES_TABLE . ' WHERE Published>0 AND Url LIKE "' . $GLOBALS['DB_WE']->escape($urlLookingFor) . '" LIMIT 1'))){
		/**
		 * we check if the given URL and DB Url are identical
		 * if not we redirect to the DB url to avoid dublicate content
		 */
		if($object['Url'] !== $urlLookingFor){
			header("Location: " . str_replace($urlLookingFor, $object['Url'], WE_REDIRECTED_SEO), true, 301);
			exit;
		}
		break;
	} else {//reduce the rest of the given url and try again
		$urlLookingFor = ltrim(stristr($urlLookingFor, "/"), "/");
	}
}

/**
 * now we try to get the trigger document
 */
if($object && $object['ID']){
	$docPathOfUrl = substr(WE_REDIRECTED_SEO, 0, strripos(WE_REDIRECTED_SEO, $urlLookingFor)); //cut the known seo-url from object of the whole URL
	
	//get trigger document by url and/or (extra) workspaces by object properties
	$triggerDocPath = we_objectFile::getNextDynDoc(($path = rtrim($docPathOfUrl, "/") . '.php'), $object['ParentID'], $object['Workspaces'], $object['ExtraWorkspacesSelected'], $GLOBALS['DB_WE']);
	
	if(!$triggerDocPath){//fallback
		if(NAVIGATION_DIRECTORYINDEX_NAMES){ //now we try to get trigger doc by the given SEO-URL and NAVIGATION_DIRECTORYINDEX_NAMES from preferences
			$dirIndexArray = array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES));
			foreach($dirIndexArray as $dirIndex){
				if(($triggerID = intval(f('SELECT ID FROM ' . FILE_TABLE . ' WHERE Published>0 AND IsDynamic=1 AND Path="' . $GLOBALS['DB_WE']->escape($docPathOfUrl . $dirIndex) . '" LIMIT 1')))){
					$triggerDocPath = id_to_path($triggerID, FILE_TABLE);
					break;
				}
			}	
		}
		
		$triggerDocPath = $triggerDocPath ? : //we use the default trigger document of object class
			(($object['TriggerID'] && ($isDynamic = f('SELECT IsDynamic FROM ' . FILE_TABLE . ' WHERE ID=' . intval($object['TriggerID'])))) ? 
				id_to_path($object['TriggerID'], FILE_TABLE) : 
				false);
	}

	if($triggerDocPath){// now we have an object and a trigger document
		//remove all cookies from Request String if set (if not, cookies are exposed on listviews etc & max interfer with given Cookies)
		$_REQUEST = array_merge($_GET, $_POST);

		//get query string if there
		$urlQueryString = array();
		if(!empty($_SERVER['REDIRECT_QUERY_STRING'])){
			parse_str($_SERVER['REDIRECT_QUERY_STRING'], $urlQueryString);
		} elseif(!empty($_SERVER['QUERY_STRING'])){
			parse_str($_SERVER['QUERY_STRING'], $urlQueryString);
		}

		//should we also send $_GET?
		$_GET = isset($_GET) ? array_merge($_GET, $urlQueryString) : $urlQueryString;

		$_REQUEST = isset($_REQUEST) ? array_merge($_REQUEST, $urlQueryString) : $urlQueryString;
		$_REQUEST['we_objectID'] = $object['ID'];
		$_REQUEST['we_oid'] = $object['ID'];

		$_SERVER['SCRIPT_NAME'] = $triggerDocPath;

		we_html_tools::setHttpCode(200);
		include(WEBEDITION_PATH . '../' . $_SERVER['SCRIPT_NAME']);
		exit;
	}
}

/**
 * nothing found show errorDoc404
 */
if(ERROR_DOCUMENT_NO_OBJECTFILE){
	we_html_tools::setHttpCode(SUPPRESS404CODE ? 200 : 404);
	if(FORCE404REDIRECT){
		header("Location: " . ($path = id_to_path(ERROR_DOCUMENT_NO_OBJECTFILE, FILE_TABLE)));
		exit;
	} else {
		include(WEBEDITION_PATH . '../' . id_to_path(ERROR_DOCUMENT_NO_OBJECTFILE, FILE_TABLE));
	}
}

exit;
