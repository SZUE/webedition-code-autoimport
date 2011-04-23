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

function we_tag_categorySelect($attribs, $content){
	$name = we_getTagAttribute("name", $attribs);
	$isuserinput = (strlen($name) == 0);
	$name = $isuserinput ? "we_ui_" . $GLOBALS["WE_FORM"] . "_categories" : $name;

	$type = we_getTagAttribute("type", $attribs);
	$rootdir = we_getTagAttribute("rootdir", $attribs, "/");
	$firstentry = we_getTagAttribute("firstentry", $attribs);
	$showpath = we_getTagAttribute("showpath", $attribs, "", true);
	$indent = we_getTagAttribute("indent", $attribs, "");

	$values = "";
	if ($isuserinput && $GLOBALS["WE_FORM"]) {
		$objekt = isset($GLOBALS["we_object"][$GLOBALS["WE_FORM"]]) ? $GLOBALS["we_object"][$GLOBALS["WE_FORM"]] : (isset(
				$GLOBALS["we_document"][$GLOBALS["WE_FORM"]]) ? $GLOBALS["we_document"][$GLOBALS["WE_FORM"]] : (isset(
				$GLOBALS["we_doc"]) ? $GLOBALS["we_doc"] : false));
		if ($objekt) {
			$values = $objekt->Category;
		}
		$valuesArray = makeArrayFromCSV(id_to_path($values, CATEGORY_TABLE));
	} else {
		if ($type == "request") {
			// Bug Fix #750
			if (isset($_REQUEST[$name])) {
				if (is_array($_REQUEST[$name])) {
					$values = implode(",", $_REQUEST[$name]);
				} else {
					$values = $_REQUEST[$name];
				}
			} else {
				$values = "";
			}
		} else {
			// Bug Fix #750
			if (isset($GLOBALS[$name]) && is_array($GLOBALS[$name])) {
				$values = implode(",", $GLOBALS[$name]);
			} else {
				$values = $GLOBALS[$name];
			}
		}
		$valuesArray = makeArrayFromCSV($values, CATEGORY_TABLE);
	}

	$attribs["name"] = $name;

	// Bug Fix #750
	if (isset($attribs["multiple"]) && $attribs["multiple"] == "true") {
		$attribs["name"] .= "[]";
		$attribs["multiple"] = "multiple";
	} else {
		$attribs = removeAttribs($attribs, array(
			'size', 'multiple'
		));
	}

	$attribs = removeAttribs($attribs, array(
		'showpath', 'rootdir', 'firstentry', 'type'
	));

	if (!$content) {
		if ($firstentry) {
			$content .= getHtmlTag('option', array(
				'value' => ''
			), $firstentry) . "\n";
		}
		$db = new DB_WE();
		$dbfield = $showpath ? "Path" : "Category";
		$db->query("SELECT Path,Category FROM " . CATEGORY_TABLE . " WHERE Path like '".$db->escape($rootdir)."%' ORDER BY $dbfield");
		while ($db->next_record()) {
			$deep = sizeof(explode("/", $db->f('Path'))) - 2;
			$field = $db->f($dbfield);
			if ($rootdir && $rootdir != "/" && $showpath) {
				$field = ereg_replace('^' . quotemeta($rootdir) . '(.*)$', '\1', $field);
			}
			if ($field) {
				if (in_array($db->f("Path"), $valuesArray)) {
					$content .= getHtmlTag(
							'option',
							array(
								'value' => $db->f("Path"), 'selected' => 'selected'
							),
							str_repeat($indent, $deep) . $field) . "\n";
				} else {
					$content .= getHtmlTag('option', array(
						'value' => $db->f("Path")
					), str_repeat($indent, $deep) . $field) . "\n";
				}
			}
		}
	} else {
		foreach ($valuesArray as $catPaths) {
			if (stripos($content,"<option>")!==false) {
				$content = eregi_replace(
						'<option>' . quotemeta($catPaths) . '( ?[<\n\r\t])',
						'<option selected="selected">' . $catPaths . '\1',
						$content);
			}
			$content = str_replace(
					'<option value="' . $catPaths . '">',
					'<option value="' . $catPaths . '" selected="selected">',
					$content);
		}
	}
	return getHtmlTag('select', $attribs, $content, true) . "\n";
}
