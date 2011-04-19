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

function we_tag_pageLanguage($attribs, $content){

	$docAttr = we_getTagAttribute("doc", $attribs, "self");
	$type = we_getTagAttribute("type", $attribs, "complete");
	$case = we_getTagAttribute("case", $attribs, "unchanged");
	$nameTo = we_getTagAttribute("nameto", $attribs);
	$to = we_getTagAttribute("to", $attribs,'screen');

	$doc = we_getDocForTag($docAttr);

	$lang=$doc->Language;
	$out="";

	switch ($type){
		case "language":
			$out=substr($lang,0,2);
			break;
		case "country":
			$out=substr($lang,3,2);
			break;
		default:
			$out=$lang;
	}
	switch ($case){
		case "uppercase":
			$out= strtoupper ($out);
			break;
		case "lowercase":
			$out= strtolower ($out);
			break;
		default:
			$out=$out;
	}
	switch ($to) {
		case "request" :
			$_REQUEST[$nameTo] = $out;
			break;
		case "post" :
			$_POST[$nameTo] = $out;
			break;
		case "get" :
			$_GET[$nameTo] = $out;
			break;
		case "global" :
			$GLOBALS[$nameTo] = $out;
			break;
		case "session" :
			$_SESSION[$nameTo] = $out;
			break;
		case "top" :
			$GLOBALS["WE_MAIN_DOC_REF"]->setElement($nameTo, $out);
			break;
		case "block" :
		case "self" :
			$GLOBALS["we_doc"]->setElement($nameTo, $out);
			break;
		case "sessionfield" :
			if (isset($_SESSION["webuser"][$nameTo])){
				$_SESSION["webuser"][$nameTo] = $out;
			}
			break;
		case "screen": return $out;
	}
	return null;

}
