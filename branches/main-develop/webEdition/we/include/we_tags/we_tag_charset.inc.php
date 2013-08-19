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
function we_parse_tag_charset($attribs, $content){
	return '<?php printElement(' . we_tag_tagParser::printTag('charset', $attribs, $content, true) . ');?>';
}

function we_tag_charset($attribs, $content){
	$defined = weTag_getAttribute('defined', $attribs);

	if($GLOBALS['we_doc']->EditPageNr == WE_EDITPAGE_PROPERTIES && $GLOBALS['we_doc']->InWebEdition){ //	normally meta tags are edited on property page
		$GLOBALS['meta']['Charset']['default'] = $content;
		$GLOBALS['meta']['Charset']['defined'] = $defined;
		return '';
	} else{
		$content = isset($GLOBALS['CHARSET']) && $GLOBALS['CHARSET'] ? $GLOBALS['CHARSET'] : $content;

		if(!empty($content)){ //	set charset
			$attribs['http-equiv'] = 'Content-Type';
			$attribs['content'] = 'text/html; charset=' . $content;

			$attribs = removeAttribs($attribs, array('defined'));

			return getHtmlTag('meta', $attribs) . "\n";
		} else{
			return '';
		}
	}
}
