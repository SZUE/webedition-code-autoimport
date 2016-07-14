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
function we_tag_position(array $attribs){

	//	type is required !!!
	if(($missingAttrib = attributFehltError($attribs, "type", __FUNCTION__))){
		echo $missingAttrib;
		return '';
	}

	//	here we get the needed attributes
	$reference = weTag_getAttribute("reference", $attribs, '', we_base_request::STRING);
	//	this value we will return later
	$retPos = "";

	switch(($type = weTag_getAttribute("type", $attribs, '', we_base_request::STRING))){

		case "listview" : //	inside a listview, we take direct global listview object
			$retPos = ($GLOBALS['lv']->start + $GLOBALS['lv']->count);
			break;

		case "listdir" : //	inside a listview
			if(isset($GLOBALS['we_position']['listdir'])){
				$content = $GLOBALS['we_position']['listdir'];
			}
			if(isset($content) && $content['position']){
				$retPos = $content['position'];
			}
			break;

		case "linklist" : //	look in fkt we_tag_linklist and class we_linklist for details
			$missingAttrib = attributFehltError($attribs, "reference", __FUNCTION__); // seperate because of #6890
			if($missingAttrib){
				echo $missingAttrib;
				return "";
			}
			foreach($GLOBALS['we_position'][$type] as $name => $arr){

				if(strpos($name, $reference) === 0){
					if(is_array($arr)){
						$content = $arr;
					}
				}
			}
			if(isset($content) && isset($content['position'])){
				$retPos = $content['position']; //  #6890
			}
			break;

		case "block" : //	look in function we_tag_block for details
			//	first we must get right array !!!
			$missingAttrib = attributFehltError($attribs, "reference", __FUNCTION__);
			if($missingAttrib){
				echo $missingAttrib;
				return "";
			}
			foreach($GLOBALS['we_position'][$type] as $name => $arr){

				if(strpos($name, $reference) === 0){
					if(is_array($arr)){
						$content = $arr;
					}
				}
			}
			if(isset($content) && $content['position']){
				$retPos = $content['position'];
			}
			break;
	}

	//	convert to desired format
	switch(weTag_getAttribute("format", $attribs, 1, we_base_request::STRING)){
		case 'a' :
			return we_base_util::number2System($retPos);
		case 'A' :
			return strtoupper(we_base_util::number2System($retPos));
		default :
			return $retPos;
	}
}
