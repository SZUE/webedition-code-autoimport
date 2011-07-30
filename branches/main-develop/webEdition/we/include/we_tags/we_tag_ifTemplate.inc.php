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

function we_tag_ifTemplate($attribs, $content){
	$id = we_getTagAttribute("id", $attribs);
	$workspaceID = we_getTagAttribute("workspaceID", $attribs);
	$path = we_getTagAttribute("path", $attribs);

	if (isset($GLOBALS['we_doc']->TemplateID) && $id !== "") {
		$idArray = makeArrayFromCSV($id);
		return in_array($GLOBALS['we_doc']->TemplateID, $idArray);
	} else {
		if ($workspaceID !== "") {
			$TempPath = $_SERVER['DOCUMENT_ROOT']."/webEdition/we/templates";
			if (isset($GLOBALS['we_doc']->TemplatePath)) { // in documents
				$curTempPath = $GLOBALS['we_doc']->TemplatePath;
				$curTempPath = str_replace($TempPath,'',$curTempPath);
			} else { // in templates
				$curTempPath = $GLOBALS['we_doc']->Path;
			}
			$row = getHash("SELECT DISTINCT Path FROM " . TEMPLATES_TABLE . " WHERE ID=".abs($workspaceID)." LIMIT 1", new DB_WE());
			if (isset($row['Path']) && strpos($curTempPath,$row['Path']) !== false && strpos($curTempPath,$row['Path'])==0) { return true; } else {return false;}
		} else {
			if ($path === "") {
				return true;
			}
			if (isset($GLOBALS['we_doc']->TemplatePath)) {
				$pathReg = "|^" . str_replace("\\*", ".*", preg_quote($path, "|")) . "\$|";
				return preg_match($pathReg, $GLOBALS['we_doc']->TemplatePath);
			}
		}
	}
	return false;
}
