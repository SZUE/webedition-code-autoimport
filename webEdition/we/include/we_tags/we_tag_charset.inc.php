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
function we_tag_charset(array $attribs, $content){
	$content = !empty($GLOBALS['CHARSET']) ? $GLOBALS['CHARSET'] : $content;
	if(!empty($GLOBALS['we_editmode']) && $GLOBALS['we_doc']->EditPageNr === we_base_constants::WE_EDITPAGE_PROPERTIES){
		//set meta data & exit
		$GLOBALS['meta']['Charset'] = ['default' => $content,
			'defined' => weTag_getAttribute('defined', $attribs, '', we_base_request::STRING),
		];
		return;
	}

	if($content){ //	set charset
		$attribs['charset'] = $content;
		if(!headers_sent()){
			header('Content-Type: ' . 'text/html; charset=' . $content);
		}

		return getHtmlTag('meta', removeAttribs($attribs, ['defined'])) . "\n";
	}
	return '';
}
