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
	global $we_editmode;

	// check for id attribute
	$myRequest=array();
	if(isset($_SERVER['REDIRECT_QUERY_STRING']) && $_SERVER['REDIRECT_QUERY_STRING']!=''){parse_str($_SERVER['REDIRECT_QUERY_STRING'],$myRequest);}
	

	// get attributes
	$error404doc = we_getTagAttribute("error404doc", $attribs);
	$path_parts = pathinfo($_SERVER['SCRIPT_URL']);
	
	if(!$we_editmode){
		$db = new DB_WE();
		$displayid=0;
		$objectid=0;
		$searchfor ='';
		$notfound=true;
		while($notfound && $path_parts['dirname']!='/'){
			$display=$path_parts['dirname'].DEFAULT_DYNAMIC_EXT;
			$displayid=abs(f("SELECT DISTINCT ID FROM ".FILE_TABLE." WHERE Path='" . mysql_real_escape_string($display) . "' LIMIT 1", "ID", $db));
			if ($searchfor){
				$searchfor = $path_parts['filename'].DIRECTORY_SEPARATOR.$searchfor;
			} else $searchfor = $path_parts['filename'];		
			if($displayid){		
				$objectid=abs(f("SELECT DISTINCT ID FROM ".OBJECT_FILES_TABLE." WHERE Url='" . mysql_real_escape_string($searchfor) . "' LIMIT 1", "ID", $db));
				if ($objectid){
					$notfound=false;
				} else {
					$path_parts = pathinfo($path_parts['dirname']);
				}
			} else {
				$path_parts = pathinfo($path_parts['dirname']);
			}
		}
		if(!$notfound){
			$_REQUEST=array_merge($_REQUEST,$myRequest);
			$_REQUEST['we_objectID']=$objectid;
			unset($GLOBALS["WE_MAIN_DOC"]);
			header("HTTP/1.0 200 OK", true,200);
			header("Status: 200 OK", true,200);
			include($_SERVER["DOCUMENT_ROOT"] . $display);
			//we_tag('include', array('type'=>'document', 'id'=>$displayid,'gethttp'=>'0'));
			exit;
		} elseif($error404doc) {
			header("HTTP/1.0 404 Not Found", true,404);
			header("Status: 404 Not Found", true,404);
			we_tag('include', array('type'=>'document', 'id'=>$error404doc,'gethttp'=>'0'));
			exit;
		}
	} 	
}
