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
function we_tag_css($attribs){
	if(($foo = attributFehltError($attribs, 'id', __FUNCTION__))){
		return $foo;
	}

	$row = getHash('SELECT Path,IsFolder,Published FROM ' . FILE_TABLE . ' WHERE Published>0 AND ID=' . intval(weTag_getAttribute('id', $attribs, 0, we_base_request::INT)));
	if(!$row){
		return '';
	}

	$nolink = false;
	switch(weTag_getAttribute('applyto', $attribs, defined('CSSAPPLYTO_DEFAULT') ? CSSAPPLYTO_DEFAULT : 'around', we_base_request::STRING)){
		case 'around' :
			break;
		case 'wysiwyg' :
			$nolink = true;
		case 'all' :
			switch(weTag_getAttribute('media', $attribs, '', we_base_request::STRING)){
				case '':
				case 'screen':
				case 'all':
					$GLOBALS['we_doc']->addDocumentCss($row['Path'] . '?m=' . $row['Published']);
					break;
			}
			break;
	}
	//	remove not needed elements
	$attribs = removeAttribs($attribs, array('id', 'applyto'));

	$attribs['rel'] = weTag_getAttribute('rel', $attribs, 'stylesheet', we_base_request::STRING);
	$attribs['type'] = 'text/css';
	$attribs['href'] = $row['Path'] . ($row['IsFolder'] ? '/' : '') . '?m=' . $row['Published'];

	return $nolink ? '' : getHtmlTag('link', $attribs) . "\n";
}
