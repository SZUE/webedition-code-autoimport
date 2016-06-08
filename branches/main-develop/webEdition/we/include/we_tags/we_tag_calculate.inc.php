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
function we_parse_tag_calculate($a, $content, array $attribs){
	$attribs['_type'] = 'stop';
	return '<?php ' . we_tag_tagParser::printTag('calculate', array('_type' => 'start')) . ';?>' . $content . '<?php printElement(' . we_tag_tagParser::printTag('calculate', $attribs) . ');?>';
}

function we_tag_calculate(array $attribs, $content){
	//internal Attribute
	switch(weTag_getAttribute('_type', $attribs, '', we_base_request::STRING)){
		case 'start':
			$GLOBALS['calculate'] = 1;
			ob_start();
			return;
		case 'stop':
			$content = ob_get_clean();
			unset($GLOBALS['calculate']);
			$sum = weTag_getAttribute('sum', $attribs, '', we_base_request::STRING);
			$num_format = weTag_getAttribute('num_format', $attribs, '', we_base_request::STRING);
			$print = weTag_getAttribute('print', $attribs, true, we_base_request::BOOL);
			eval('$result = (' . (trim($content) ? $content : 0) . ') ;');
			if(!isset($result)){
				$result = 0;
			}

			if($sum){
				if(!isset($GLOBALS['summe'][$sum])){
					$GLOBALS['summe'][$sum] = 0;
				}
				$GLOBALS['summe'][$sum] += $result;
			}
			return ($print ? ($num_format ? we_base_util::formatNumber($result, $num_format, intval(weTag_getAttribute('decimals', $attribs, 2, we_base_request::INT))) : $result) : '');
		default:
			return attributFehltError($attribs, '_type', __FUNCTION__);
	}
}
