<?php
function we_tag_ifTemplate($attribs, $content){
	$id = we_getTagAttribute("id", $attribs);
	$workspaceID = we_getTagAttribute("workspaceID", $attribs);
	$path = we_getTagAttribute("path", $attribs);

	if (isset($GLOBALS['we_doc']->TemplateID) && $id !== "") {
		$idArray = makeArrayFromCSV($id);
		return in_array($GLOBALS['we_doc']->TemplateID, $idArray);
	} else {
		if ($workspaceID !== "") {
			$TempPath = $_SERVER["DOCUMENT_ROOT"]."/webEdition/we/templates";
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
}?>
