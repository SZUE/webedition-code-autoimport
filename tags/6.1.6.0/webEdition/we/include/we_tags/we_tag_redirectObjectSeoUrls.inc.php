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

function we_tag_redirectObjectSeoUrls($attribs, $content){

	// check for id attribute
	$myRequest=array();
	if(isset($_SERVER['REDIRECT_QUERY_STRING']) && $_SERVER['REDIRECT_QUERY_STRING']!=''){parse_str($_SERVER['REDIRECT_QUERY_STRING'],$myRequest);}


	// get attributes
	$error404doc = we_getTagAttribute("error404doc", $attribs);
	$hiddendirindex = we_getTagAttribute("hiddendirindex", $attribs,"false",true);
	$suppresserrorcode = we_getTagAttribute("suppresserrorcode", $attribs,"false",true);
	if($hiddendirindex){
		if (defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !=''){
			$dirindexarray = explode(',',NAVIGATION_DIRECTORYINDEX_NAMES);
		} else {
			$hiddendirindex = false;
		}
	}

	if (isset($_SERVER['SCRIPT_URL']) && $_SERVER['SCRIPT_URL']!=''){
		$path_parts = pathinfo($_SERVER['SCRIPT_URL']);
	} elseif(isset($_SERVER['REDIRECT_URL']) && $_SERVER['REDIRECT_URL']!=''){
		$path_parts = pathinfo($_SERVER['REDIRECT_URL']);
	}

	if(!$GLOBALS['we_editmode']){
		$db = new DB_WE();
		$displayid=0;
		$objectid=0;
		$searchfor ='';
		$notfound=true;
		while($notfound && isset($path_parts['dirname']) && $path_parts['dirname']!='/'){

			$display=$path_parts['dirname'].DEFAULT_DYNAMIC_EXT;
			$displayid=abs(f("SELECT DISTINCT ID FROM ".FILE_TABLE." WHERE Path='" . $db->escape($display) . "' LIMIT 1", "ID", $db));
			if ($searchfor){
				$searchfor = $path_parts['basename'].'/'.$searchfor;
			} else $searchfor = $path_parts['basename'];
			if(!$displayid && $hiddendirindex){
				foreach($dirindexarray as $dirindex){
					$display=$path_parts['dirname'].'/'.$dirindex;
					$displayidtest=abs(f("SELECT DISTINCT ID FROM ".FILE_TABLE." WHERE Path='" . $db->escape($display) . "' LIMIT 1", "ID", $db));
					if($displayidtest)$displayid = $displayidtest;
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

			foreach($dirindexarray as $dirindex){
				$display=$path_parts['dirname'].$dirindex;
				$displayidtest=abs(f("SELECT DISTINCT ID FROM ".FILE_TABLE." WHERE Path='" . escape_sql_query($display) . "' LIMIT 1", "ID", $db));
				if($displayidtest)$displayid = $displayidtest;
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
			unset($GLOBALS["WE_MAIN_DOC"]);
			unset($GLOBALS["we_doc"]);
			$saveLang= $GLOBALS['WE_LANGUAGE'];
			header("HTTP/1.0 200 OK", true,200);
			header("Status: 200 OK", true,200);
			include($_SERVER["DOCUMENT_ROOT"] . $display);
			//we_tag('include', array('type'=>'document', 'id'=>$displayid,'gethttp'=>'0'));
			exit;
		} elseif($error404doc) {
			if(suppresserrorcode){
				header("HTTP/1.0 200 OK", true,200);
				header("Status: 200 OK", true,200);
			} else {
				header("HTTP/1.0 404 Not Found", true,404);
				header("Status: 404 Not Found", true,404);
			}
			we_tag('include', array('type'=>'document', 'id'=>$error404doc,'gethttp'=>'0'));
			exit;
		}
	}
}
