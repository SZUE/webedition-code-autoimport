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

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tools/navigation/class/weNavigationItems.class.php');

function we_tag_navigationEntry($attribs, $content = ''){
	if (($foo = attributFehltError($attribs, 'type', 'navigation'))){
		return $foo;
	}

	$navigationName = we_getTagAttribute('navigationname', $attribs, "default");
	$type = we_getTagAttribute('type', $attribs);
	$level = we_getTagAttribute('level', $attribs, 'defaultLevel');
	$current = we_getTagAttribute('current', $attribs, 'defaultCurrent');
	$position = we_getTagAttribute('position', $attribs, 'defaultPosition');

	$tp = new we_tagParser($content);

	$tp->parseTags($content);

	$_positions = makeArrayFromCSV($position);

	foreach ($_positions as $position) {
		if ($position == 'first') {
			$position = 1;
		}
		$GLOBALS['we_navigation'][$navigationName]->setTemplate($content, $type, $level, $current, $position);
	}
}
