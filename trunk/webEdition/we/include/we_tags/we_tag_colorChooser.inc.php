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
function we_tag_colorChooser(array $attribs){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}

	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);

	if(!$GLOBALS['we_doc']->getElement($attribs['name']) && !empty($attribs['value'])){
		$GLOBALS['we_doc']->setElement($attribs['name'], $attribs['value']);
	}

	if($GLOBALS['we_editmode']){
		$width = weTag_getAttribute('width', $attribs, 100, we_base_request::UNIT);
		$height = weTag_getAttribute('height', $attribs, 18, we_base_request::UNIT);
		return $GLOBALS['we_doc']->formColor($width, $name, 'attrib', $height);
	}
	return $GLOBALS['we_doc']->getElement($name);
}
