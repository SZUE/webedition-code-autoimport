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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function we_tag_pageLanguage($attribs){

	$docAttr = weTag_getAttribute("doc", $attribs, "self");
	$type = weTag_getAttribute("type", $attribs, "complete");
	$case = weTag_getAttribute("case", $attribs, "unchanged");
	$doc = we_getDocForTag($docAttr);

	$lang = $doc->Language;
	$out = "";

	switch($type){
		case "language":
			$out = substr($lang, 0, 2);
			break;
		case "country":
			$out = substr($lang, 3, 2);
			break;
		//#7279 start
		case "language_name":
			$out = Zend_Locale::getTranslation(substr($lang, 0, 2), 'language',substr($lang, 0, 2));
			break;
		case "country_name":
			$out = Zend_Locale::getTranslation(substr($lang, 3, 2), 'country',substr($lang, 3, 2));
			break;
		//#7279 end
		default:
			$out = $lang;
	}
	switch($case){
		case "uppercase":
			$out = strtoupper($out);
			break;
		case "lowercase":
			$out = strtolower($out);
			break;
		default:
			$out = $out;
	}
	return $out;
}
