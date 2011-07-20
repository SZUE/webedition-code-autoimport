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

function we_tag_url($attribs, $content){
	$foo = attributFehltError($attribs, "id", "url");
	if ($foo)
		return $foo;
	static $urls = array();
	static $objurls = array();
	$type=we_getTagAttribute("type", $attribs,'document');
	$id = we_getTagAttribute("id", $attribs);
	$nameTo = we_getTagAttribute('nameto', $attribs);
	$to = we_getTagAttribute('to', $attribs, 'screen');
	$triggerid = we_getTagAttribute("triggerid", $attribs,'0');
	if (defined('TAGLINKS_DIRECTORYINDEX_HIDE') && TAGLINKS_DIRECTORYINDEX_HIDE){
		$hidedirindex = we_getTagAttribute("hidedirindex", $attribs, "true", true,true);
	} else {
		$hidedirindex = we_getTagAttribute("hidedirindex", $attribs, "false", true);
	}
	if (defined('TAGLINKS_OBJECTSEOURLS') && TAGLINKS_OBJECTSEOURLS){
		$objectseourls = we_getTagAttribute("objectseourls", $attribs, "true", true,true);
	} else {
		$objectseourls = we_getTagAttribute("objectseourls", $attribs, "false", true);
	}
	if ($type=='document'){
		if (isset($urls[$id])) { // do only work you have never done before
			return we_redirect_tagoutput($urls[$id], $nameTo, $to);
		}
	} else {
		if (isset($objurls[$id])) { // do only work you have never done before
			return we_redirect_tagoutput($objurls[$id], $nameTo, $to);
		}
	}
	if ($id == '0') {
		$url = "/";
	} else {
		$urlNotSet=true;
	    if ( ($id=='self' || $id=='top') && $type=='document'){
			$doc = we_getDocForTag($id, true); // check if we should use the top document or the  included document
			$testid = $doc->ID;
			if ($id=='top'){//check for object
			
				if(isset($GLOBALS['WE_MAIN_DOC']->TableID)){//ein object
					if (!$triggerid){
							$triggerid=$GLOBALS['WE_MAIN_DOC']->ID;
					}
					$path_parts = pathinfo(id_to_path($triggerid));
					if ($objectseourls && $GLOBALS['WE_MAIN_DOC']->Url!='' && show_SeoLinks() ){
						if (show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && $hidedirindex && in_array($path_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES)) ){
							$url = ($path_parts['dirname']!='/' ? $path_parts['dirname']:'').'/'.$GLOBALS['WE_MAIN_DOC']->Url;
						} else {
							$url = ($path_parts['dirname']!='/' ? $path_parts['dirname']:'').'/'.$path_parts['filename'].'/'.$GLOBALS['WE_MAIN_DOC']->Url;
						}
					} else {
						if (show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && $hidedirindex && in_array($path_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES)) ){
							$url = ($path_parts['dirname']!='/' ? $path_parts['dirname']:'').'/'."?we_objectID=".$GLOBALS['WE_MAIN_DOC']->OF_ID;
						} else {
							$url = $GLOBALS['WE_MAIN_DOC']->Path."?we_objectID=".$GLOBALS['WE_MAIN_DOC']->OF_ID;
						}
					}
					$urlNotSet=false;
				}
			}
		} else {
			$testid = $id;
		}
		if($urlNotSet){
			if ($type=='document'){
				$row = getHash("SELECT Path,IsFolder,IsDynamic FROM " . FILE_TABLE . " WHERE ID=".abs($testid)."", new DB_WE());
				$url = isset($row["Path"]) ? ($row["Path"] . ($row["IsFolder"] ? "/" : "")) : "";
				$path_parts = pathinfo($url);
				if (show_SeoLinks() && $hidedirindex && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && defined('TAGLINKS_DIRECTORYINDEX_HIDE') && TAGLINKS_DIRECTORYINDEX_HIDE  && in_array($path_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES)) ){
					$url = ($path_parts['dirname']!='/' ? $path_parts['dirname']:'').'/';
				} 
			} else {
				$row = getHash("SELECT ID,Url,TriggerID FROM " . OBJECT_FILES_TABLE . " WHERE ID=".abs($testid)."", new DB_WE());
				if (!$triggerid){
					if ($row['TriggerID']){
						$triggerid=$row['TriggerID'];
					} else {
						$triggerid=$GLOBALS['WE_MAIN_DOC']->ID;
					}	 
				}
				$path_parts = pathinfo(id_to_path($triggerid));
				if ($objectseourls && $row['Url']!='' && show_SeoLinks() ){
					if (show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && $hidedirindex && in_array($path_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES)) ){
						$url = ($path_parts['dirname']!='/' ? $path_parts['dirname']:'').'/'. $row['Url'];
					} else {
						$url = ($path_parts['dirname']!='/' ? $path_parts['dirname']:'').'/'.$path_parts['filename'].'/'.$row['Url'];
					}
				} else {
					if (show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && $hidedirindex && in_array($path_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES)) ){
						$url = ($path_parts['dirname']!='/' ? $path_parts['dirname']:'').'/'."?we_objectID=".$row['ID'];
					} else {
						$url = id_to_path($triggerid)."?we_objectID=".$row['ID'];
					}
				}
			}
		}
	}
	if ($type=='document'){
		$urls[$id] = $url;
	} else {
		$objurls[$id] = $url;
	}
	return we_redirect_tagoutput($url, $nameTo, $to);

}
