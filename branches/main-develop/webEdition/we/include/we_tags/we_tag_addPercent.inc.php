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
include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/we_util.inc.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/lib/we/util/Strings.php');

function we_parse_tag_addPercent($attribs, $content) {
	eval('$attribs = ' . $attribs . ';');
	$attribs['_type'] = 'stop';
	return '<?php ' . we_tagParser::printTag('addPercent', array('_type' => 'start')) . ';?>' . $content . '<?php printElement(' . we_tagParser::printTag('addPercent', $attribs) . ');?>';
}

function we_tag_addPercent($attribs, $content) {
	//internal Attribute
	$_type = we_getTagAttribute('_type', $attribs);
	switch ($_type) {
		case 'start':
			$GLOBALS['calculate'] = 1;
			ob_start();
			return;
		case 'stop':
			$content = we_util::std_numberformat(ob_get_contents());
			ob_end_clean();
			unset($GLOBALS['calculate']);
			if (($foo = attributFehltError($attribs, 'percent', 'addPercent'))) {
				return $foo;
			}
			$percent = we_getTagAttribute('percent', $attribs);
			$num_format = we_getTagAttribute('num_format', $attribs);
			$result = ($content / 100) * (100 + $percent);
			return we_util_Strings::formatnumber($result, $num_format);
		default:
			return attributFehltError($attribs, 'percent', '_type');
	}
}
