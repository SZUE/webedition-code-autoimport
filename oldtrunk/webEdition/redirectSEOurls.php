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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

require_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");

$myRequest=array();
if(isset($_SERVER['REDIRECT_QUERY_STRING']) && $_SERVER['REDIRECT_QUERY_STRING']!=''){
	parse_str($_SERVER['REDIRECT_QUERY_STRING'],$myRequest);
} elseif(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']!='' && strpos($_SERVER['REQUEST_URI'],'?')!==false ) {
	$zw= explode('?',$_SERVER['REQUEST_URI']);
	parse_str($zw[1],$myRequest);
}


// get attributes
if (defined('ERROR_DOCUMENT_NO_OBJECTFILE') && ERROR_DOCUMENT_NO_OBJECTFILE){
	$error404doc = ERROR_DOCUMENT_NO_OBJECTFILE;
} else {
	$error404doc = 0;
}

if (defined('SUPPRESS404CODE') && SUPPRESS404CODE){
	$suppresserrorcode = true;
} else {
	$suppresserrorcode = false;
}
if (defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && ( (defined('NAVIGATION_DIRECTORYINDEX_HIDE') && NAVIGATION_DIRECTORYINDEX_HIDE ) || (defined('WYSIWYGLINKS_DIRECTORYINDEX_HIDE') && WYSIWYGLINKS_DIRECTORYINDEX_HIDE ) || (defined('TAGLINKS_DIRECTORYINDEX_HIDE') && TAGLINKS_DIRECTORYINDEX_HIDE ))  ){
	$dirindexarray = explode(',',NAVIGATION_DIRECTORYINDEX_NAMES);
	$keys = array_keys($dirindexarray);
	foreach ($keys as $key) {
		$dirindexarray[$key] = trim ($dirindexarray[$key]);
	}
	$hiddendirindex = true;
} else {
	$hiddendirindex = false;
}


if (isset($_SERVER['SCRIPT_URL']) && $_SERVER['SCRIPT_URL']!=''){
	$path_parts = pathinfo($_SERVER['SCRIPT_URL']);
} elseif(isset($_SERVER['REDIRECT_URL']) && $_SERVER['REDIRECT_URL']!='' && $_SERVER['REDIRECT_URL']!='/webEdition/redirectSEOurls.php'){
	$path_parts = pathinfo($_SERVER['REDIRECT_URL']);
} elseif(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']!=''){
	if(strpos($_SERVER['REQUEST_URI'],'?')!==false){
		$zw2= explode('?',$_SERVER['REQUEST_URI']);
		$path_parts = pathinfo($zw2[0]);
	} else {
		$path_parts = pathinfo($_SERVER['REQUEST_URI']);
	}
}

if(! (isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode'])){
	$db = new DB_WE();
	$displayid=0;
	$objectid=0;
	$searchfor ='';
	$notfound=true;
	while($notfound && isset($path_parts['dirname']) && $path_parts['dirname']!='/' && $path_parts['dirname']!='\\'){

		$display=$path_parts['dirname'].DEFAULT_DYNAMIC_EXT;
		$displayid=abs(f("SELECT DISTINCT ID FROM ".FILE_TABLE." WHERE Path='" . escape_sql_query($display) . "' LIMIT 1", "ID", $db));
		if ($searchfor){
			$searchfor = $path_parts['basename'].'/'.$searchfor;
		} else $searchfor = $path_parts['basename'];
		if(!$displayid && $hiddendirindex){
			//z79
			$display = "";
			foreach($dirindexarray as $dirindex){
				$displaytest=$path_parts['dirname'].'/'.$dirindex;
				$displayidtest=abs(f("SELECT DISTINCT ID FROM ".FILE_TABLE." WHERE Path='" . escape_sql_query($displaytest) . "' LIMIT 1", "ID", $db));
				if($displayidtest){
					$displayid = $displayidtest;
					$display = $displaytest; //nur, wenn Datei vorhanden
					break; //wenn gefunden, kann man sich die weiteren Schleifen sparen.
				}
			}
		}
		if($displayid){
			if(defined('URLENCODE_OBJECTSEOURLS') && URLENCODE_OBJECTSEOURLS){
				$searchforInternal=urlencode ($searchfor);
				$searchforInternal=str_replace('%2F','/',$searchforInternal);
			} else {
				$searchforInternal=$searchfor;
			}

			$objectid=abs(f("SELECT DISTINCT ID FROM ".OBJECT_FILES_TABLE." WHERE Url='" . escape_sql_query($searchforInternal) . "' LIMIT 1", "ID", $db));
			if ($objectid){
				$notfound=false;
			} else {
				$path_parts = pathinfo($path_parts['dirname']);
			}
		} else {
			$path_parts = pathinfo($path_parts['dirname']);
		}
	}
	if($notfound && isset($path_parts['dirname']) && $path_parts['dirname']=='/' && $hiddendirindex){

		if ($searchfor){
			$searchfor = $path_parts['basename'].'/'.$searchfor;
		} else $searchfor = $path_parts['basename'];

		//z109
		$display = "";
		foreach($dirindexarray as $dirindex){
			$displaytest=$path_parts['dirname'].$dirindex;
			$displayidtest=abs(f("SELECT DISTINCT ID FROM ".FILE_TABLE." WHERE Path='" . escape_sql_query($displaytest) . "' LIMIT 1", "ID", $db));
			if($displayidtest){
				$displayid = $displayidtest;
				$display = $displaytest; //nur, wenn Datei vorhanden
				break; //wenn gefunden, kann man sich die weiteren Schleifen sparen
			}
		}
		if($displayid){
			if(defined('URLENCODE_OBJECTSEOURLS') && URLENCODE_OBJECTSEOURLS){
				$searchforInternal=urlencode ($searchfor);
				$searchforInternal=str_replace('%2F','/',$searchforInternal);
			} else {
				$searchforInternal=$searchfor;
			}
			$objectid=abs(f("SELECT DISTINCT ID FROM ".OBJECT_FILES_TABLE." WHERE Url='" . escape_sql_query($searchforInternal) . "' LIMIT 1", "ID", $db));
			if ($objectid){$notfound=false;}
		}
	}
	if(!$notfound){
		$_REQUEST=array_merge($_REQUEST,$myRequest);
		$_REQUEST['we_objectID']=$objectid;
		$_REQUEST['we_oid']=$objectid;
		$_GET=array_merge($_GET,$myRequest);
		$_SERVER["SCRIPT_NAME"]=$display;

		header("HTTP/1.0 200 OK", true,200);
		header("Status: 200 OK", true,200);
		include($_SERVER["DOCUMENT_ROOT"] . $display);

		exit;
	} elseif($error404doc) {
		if($suppresserrorcode){
			header("HTTP/1.0 200 OK", true,200);
			header("Status: 200 OK", true,200);
		} else {
			header("HTTP/1.0 404 Not Found", true,404);
			header("Status: 404 Not Found", true,404);
		}
		include($_SERVER["DOCUMENT_ROOT"] .id_to_path(ERROR_DOCUMENT_NO_OBJECTFILE, FILE_TABLE));

		exit;
	}
}
