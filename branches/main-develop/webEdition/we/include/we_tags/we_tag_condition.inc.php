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

function we_tag_condition($attribs, $content){

	$name = we_getTagAttribute("name", $attribs, "we_lv_condition");

	$GLOBALS["we_lv_conditionCount"] = isset($GLOBALS["we_lv_conditionCount"]) ? abs($GLOBALS["we_lv_conditionCount"]) : 0;

	if ($GLOBALS["we_lv_conditionCount"] == 0) {
		$GLOBALS["we_lv_conditionName"] = $name;
		$GLOBALS[$GLOBALS["we_lv_conditionName"]] = "(";
	} else {
		$GLOBALS[$GLOBALS["we_lv_conditionName"]] .= "(";
	}
	$GLOBALS["we_lv_conditionCount"]++;
	return "";
}

function we_tag_conditionAdd($attribs, $content){

	$foo = attributFehltError($attribs, "field", "conditionAdd");
	if ($foo)
		return $foo;

	// initialize possible Attributes
	$field = we_getTagAttribute("field", $attribs);
	$value = we_getTagAttribute("value", $attribs);
	$compare = we_getTagAttribute("compare", $attribs, "=");
	$var = we_getTagAttribute("var", $attribs);
	$type = we_getTagAttribute("type", $attribs);
	$property = we_getTagAttribute("property", $attribs, "", true);
	$exactmatch = we_getTagAttribute("exactmatch", $attribs, "", true);
	$docAttr = we_getTagAttribute("doc", $attribs);
	// end initialize possible Attributes


	$value = str_replace('&gt;', '>', $value);
	$value = str_replace('&lt;', '<', $value);

	$regs = array();
	if ($var && $compare == "like") {
		if (ereg('^(%)?([^%]+)(%)?$', $var, $regs)) {
			$var = $regs[2];
		}
	}
	switch (strtolower($type)) {
		case "now" :
			$value = time();
			break;
		case "sessionfield" :
			if ($var && isset($_SESSION["webuser"][$var])) {
				$value = $_SESSION["webuser"][$var];
			}
			break;
		case "document" :
			if ($var) {
				$doc = we_getDocForTag($docAttr, false);
				if ($property) {
					eval('$value = $doc->' . $var . ';');
				} else {
					$value = $doc->getElement($var);
				}
			}
			break;
		case "request" :
			if ($var && isset($_REQUEST[$var])) {
				$value = $_REQUEST[$var];
			}
			break;
		default :
			if ($var && isset($GLOBALS[$var])) {
				$value = $GLOBALS[$var];
			}
	}
	if($exactmatch && defined('DB_COLLATION') && DB_COLLATION!=''){
		if(strpos(DB_COLLATION,'latin1') !== false ) {
			$compare = "COLLATE latin1_bin ".$compare;
		} elseif(strpos(DB_COLLATION,'utf') !== false) {
			$compare = "COLLATE utf8_bin ".$compare;
		}  
	
	}
	$value = (isset($regs[1]) ? $regs[1] : "") . $value . (isset($regs[3]) ? $regs[3] : "");

	if (strlen($field) && isset($GLOBALS["we_lv_conditionName"]) && isset($GLOBALS[$GLOBALS["we_lv_conditionName"]])) {
		$GLOBALS[$GLOBALS["we_lv_conditionName"]] .= " ($field $compare '" . addslashes($value) . "') ";
	} else {
		if (eregi('^(.*)AND ?$', $GLOBALS[$GLOBALS["we_lv_conditionName"]])) {
			$GLOBALS[$GLOBALS["we_lv_conditionName"]] .= "1 ";
		} else {
			$GLOBALS[$GLOBALS["we_lv_conditionName"]] .= "0 ";
		}
	}
	return "";
}

function we_tag_conditionAND($attribs, $content){
	if (isset($GLOBALS["we_lv_conditionName"]) && isset($GLOBALS[$GLOBALS["we_lv_conditionName"]])) {
		$GLOBALS[$GLOBALS["we_lv_conditionName"]] .= " AND ";
	}
	return "";
}

function we_tag_conditionOR($attribs, $content){
	if (isset($GLOBALS["we_lv_conditionName"]) && isset($GLOBALS[$GLOBALS["we_lv_conditionName"]])) {
		$GLOBALS[$GLOBALS["we_lv_conditionName"]] .= " OR ";
	}
	return "";
}
