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
function we_tag_ifDoctype($attribs){
	if(($foo = attributFehltError($attribs, 'doctypes', __FUNCTION__))){
		print($foo);
		return false;
	}
	$matchArr = explode(',', weTag_getAttribute('doctypes', $attribs, '', we_base_request::STRING));

	$docAttr = weTag_getAttribute('doc', $attribs, 'self', we_base_request::STRING);

	if($docAttr === 'listview' && isset($GLOBALS['lv'])){
		$doctype = $GLOBALS['lv']->f('wedoc_DocType');
	} else {
		$doc = we_getDocForTag($docAttr);
		$doctype = ($doc instanceof we_webEditionDocument) ? $doc->DocType : false;
	}

	if(isset($doctype) && $doctype !== false){
		foreach($matchArr as $match){
			if($doctype == f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' WHERE DocType="' . $GLOBALS['DB_WE']->escape($match) . '"')){
				return true;
			}
		}
	}
	return false;
}
