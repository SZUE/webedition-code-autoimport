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
include_once ($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_classes/we_util.inc.php");
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/lib/we/util/Strings.php');

function we_parse_tag_calculate($attribs, $content) {
	eval('$attribs = ' . $attribs . ';');
	$attribs['_type'] = 'stop';
	return '<?php ' . we_tagParser::printTag('calculate', array('_type' => 'start')) . ';?>' . $content . '<?php printElement(' . we_tagParser::printTag('calculate', $attribs) . ');?>';
}

function we_tag_calculate($attribs, $content) {
	//internal Attribute
	$_type = we_getTagAttribute('_type', $attribs);
	switch ($_type) {
		case 'start':
			$GLOBALS['calculate'] = 1;
			ob_start();
			return;
		case 'stop':
			$content = ob_get_contents();
			ob_end_clean();
			unset($GLOBALS['calculate']);
			$sum = we_getTagAttribute("sum", $attribs);
			$num_format = we_getTagAttribute("num_format", $attribs);
			$print = we_getTagAttribute("print", $attribs, "", true, true);
			/* 	$zahl = "";
			  $content1 = "";


			  for ($x = 0; $x < strlen($content); $x++) {
			  if (ereg("[0-9|\.|,]", substr($content, $x, 1))) {
			  $zahl .= substr($content, $x, 1);
			  } else {

			  $content1 .= we_util::std_numberformat($zahl) . substr($content, $x, 1);
			  $zahl = "";
			  }
			  }
			  $content1 .= we_util::std_numberformat($zahl) . substr($content, $x, 1);
			  $content = $content1; */

			@eval('$result = (' . $content . ') ;');
			if (!isset($result)) {
				$result = 0;
			}

			if (!empty($sum)) {
				if (!isset($GLOBALS["summe"][$sum])) {
					$GLOBALS["summe"][$sum] = 0;
				}
				$GLOBALS["summe"][$sum] += $result;
			}
			return ($print ? we_util_Strings::formatnumber($result, $num_format) : '');
		default:
			return attributFehltError($attribs, 'calculate', '_type');
	}
}
