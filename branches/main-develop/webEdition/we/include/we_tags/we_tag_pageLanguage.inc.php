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
function we_tag_pageLanguage($attribs){
	$doc = we_getDocForTag(weTag_getAttribute('doc', $attribs, 'self', we_base_request::STRING));

	$lang = explode('_', $doc->Language);

	switch(weTag_getAttribute('type', $attribs, '', we_base_request::STRING)){
		case 'language':
			$out = $lang[0];
			break;
		case 'country':
			$out = $lang[1];
			break;
		case 'language_name':
			$out = we_base_country::getTranslation($lang[0], we_base_country::LANGUAGE, $lang[0]);
			break;
		case 'country_name':
			$out = we_base_country::getTranslation($lang[1], we_base_country::TERRITORY, $lang[1]);
			break;
		default:
			$out = $doc->Language;
	}

	switch(weTag_getAttribute('case', $attribs, '', we_base_request::STRING)){
		case 'uppercase':
			return strtoupper($out);
		case 'lowercase':
			return strtolower($out);
		default:
			return $out;
	}
}
