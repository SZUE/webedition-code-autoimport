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
function we_parse_tag_navigationEntry($attribs, $content){
	return '<?php ' . we_tag_tagParser::printTag('navigationEntry', $attribs, str_replace(array('global $lv;', '\\\\$'), array('', '$'), $content), true) . ';?>';
}

function we_tag_navigationEntry(array $attribs, $content){
	if(($foo = attributFehltError($attribs, 'type', __FUNCTION__))){
		echo $foo;
		return;
	}

	$navigationName = weTag_getAttribute('navigationname', $attribs, 'default', we_base_request::STRING);
	$type = weTag_getAttribute('type', $attribs, '', we_base_request::STRING);
	$level = weTag_getAttribute('level', $attribs, we_navigation_items::TEMPLATE_DEFAULT_LEVEL, we_base_request::STRING);
	$current = (isset($attribs['current']) ?
			weTag_getAttribute('current', $attribs, false, we_base_request::BOOL) :
			we_navigation_items::TEMPLATE_DEFAULT_CURRENT);

	$positions = weTag_getAttribute('position', $attribs, we_navigation_items::TEMPLATE_DEFAULT_POSITION, we_base_request::STRING_LIST);

	if(!isset($GLOBALS['we_navigation'][$navigationName])){
		echo parseError('we:navigationentry "' . $navigationName . '" not set');
		return;
	}

	foreach($positions as $position){
		if($position === 'first'){
			$position = 1;
		}
		$GLOBALS['we_navigation'][$navigationName]->setTemplate($content, $type, $level, $current, $position);
	}
}
