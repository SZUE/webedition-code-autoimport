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

	include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_tagParser.inc.php");
	include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/we_util.inc.php");

function we_tag_calculate($attribs, $content){
	$sum = we_getTagAttribute("sum", $attribs);
	$num_format = we_getTagAttribute("num_format", $attribs);
	$print = we_getTagAttribute("print", $attribs, "", true, true);

	$zahl = "";
	$content1 = "";

	$tp = new we_tagParser();
	$tags = $tp->getAllTags($content);

	$GLOBALS["calculate"] = 1;
	$tp->parseTags($tags, $content);
	$GLOBALS["calculate"] = 0;
	//echo "content : ".htmlentities($content)."<br>";


	for ($x = 0; $x < strlen($content); $x++) {
		if (ereg("[0-9|\.|,]", substr($content, $x, 1))) {
			$zahl .= substr($content, $x, 1);
		} else {

			$content1 .= we_util::std_numberformat($zahl) . substr($content, $x, 1);
			//echo "<br><br>".$x."..". $content1."<br><br>";
			$zahl = "";
		}
	}
	$content1 .= we_util::std_numberformat($zahl) . substr($content, $x, 1);
	$content = $content1;

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
	if ($num_format == "german") {
		$result = number_format($result, 2, ",", ".");
	} else
		if ($num_format == "french") {
			$result = number_format($result, 2, ",", " ");
		} else
			if ($num_format == "english") {
				$result = number_format($result, 2, ".", "");
			} else
				if ($num_format == "swiss") {
					$result = number_format($result, 2, ".", "'");
				}
	if ($print) {
		return $result;
	} else {
		return;
	}
}
