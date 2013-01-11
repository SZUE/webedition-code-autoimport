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
function we_parse_tag_keywords($attribs, $content){
	return '<?php printElement(' . we_tag_tagParser::printTag('keywords', $attribs, $content, true) . ');?>';
}

function we_tag_keywords($attribs, $content){
	$oldHtmlspecialchars = weTag_getAttribute("oldHtmlspecialchars", $attribs, false, true);
	$attribs = removeAttribs($attribs, array(
		'oldHtmlspecialchars'
		));

	if($GLOBALS['we_doc']->EditPageNr == WE_EDITPAGE_PROPERTIES && $GLOBALS['we_doc']->InWebEdition){ //	normally meta tags are edited on property page
		return '<?php	$GLOBALS["meta"]["Keywords"]["default"] = "' . str_replace('"', '\"', $content) . '"; ?>';
	}
	$keys = $GLOBALS['KEYWORDS'] ? $GLOBALS['KEYWORDS'] : $content;

	$attribs["name"] = "keywords";
	$attribs["content"] = $oldHtmlspecialchars ? oldHtmlspecialchars(strip_tags($keys)) : strip_tags($keys);
	return getHtmlTag("meta", $attribs) . "\n";
}
