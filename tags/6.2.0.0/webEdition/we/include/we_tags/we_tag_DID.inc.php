<?php
function we_tag_DID($attribs, $content){
	$docAttr = we_getTagAttribute("doc", $attribs);
	if (!$docAttr) {
		$docAttr = we_getTagAttribute("type", $attribs); // for Compatibility Reasons
	}

	switch ($docAttr) {
		case "top" :
			return $GLOBALS["WE_MAIN_DOC"]->ID;
		case "listview" :
			return $GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count - 1];
		case "self" :
		default :
			return $GLOBALS["we_doc"]->ID;
	}
}