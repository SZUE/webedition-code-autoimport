<?php
function we_tag_ifDoctype($attribs, $content){

	$foo = attributFehltError($attribs, "doctypes", "ifDoctype");
	if ($foo) {
		print($foo);
		return "";
	}
	$match = we_getTagAttribute("doctypes", $attribs);

	$docAttr = we_getTagAttribute("doc", $attribs, "self");

	if ($docAttr == "listview" && isset($GLOBALS['lv'])) {
		$doctype = $GLOBALS['lv']->f('wedoc_DocType');
	} else {
		$doc = we_getDocForTag($docAttr);
		if ($doc->ClassName == "we_template") {
			return false;
		}
		$doctype = $doc->DocType;
	}
	$matchArr = makeArrayFromCSV($match);

	if (isset($doctype) && $doctype != false) {
		foreach ($matchArr as $match) {
			$matchID = f("SELECT ID FROM " . DOC_TYPES_TABLE . " WHERE DocType='".mysql_real_escape_string($match)."'", "ID", new DB_WE());
			if ($matchID == $doctype) {
				return true;
			}
		}
	}
	return false;
}>?
