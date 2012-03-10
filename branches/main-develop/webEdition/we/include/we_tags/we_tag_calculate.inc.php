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
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/lib/we/util/Strings.php');

function we_parse_tag_calculate($attribs, $content){
	eval('$attribs = ' . $attribs . ';');
	$attribs['_type'] = 'stop';
	return '<?php ' . we_tag_tagParser::printTag('calculate', array('_type' => 'start')) . ';?>' . $content . '<?php printElement(' . we_tag_tagParser::printTag('calculate', $attribs) . ');?>';
}

function we_tag_calculate($attribs, $content){
	//internal Attribute
	$_type = weTag_getAttribute('_type', $attribs);
	switch($_type){
		case 'start':
			$GLOBALS['calculate'] = 1;
			ob_start();
			return;
		case 'stop':
			$content = ob_get_contents();
			ob_end_clean();
			unset($GLOBALS['calculate']);
			$sum = weTag_getAttribute("sum", $attribs);
			$num_format = weTag_getAttribute("num_format", $attribs);
			$print = weTag_getAttribute("print", $attribs, true, true);
			@eval('$result = (' . $content . ') ;');
			if(!isset($result)){
				$result = 0;
			}

			if(!empty($sum)){
				if(!isset($GLOBALS["summe"][$sum])){
					$GLOBALS["summe"][$sum] = 0;
				}
				$GLOBALS["summe"][$sum] += $result;
			}
			return ($print ? we_util_Strings::formatnumber($result, $num_format) : '');
		default:
			return attributFehltError($attribs, '_type', __FUNCTION__);
	}
}
