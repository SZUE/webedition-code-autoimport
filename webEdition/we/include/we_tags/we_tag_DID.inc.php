<?php
function we_tag_DID($attribs, $content){
	$docAttr = we_getTagAttribute("doc", $attribs);
	if (!$docAttr) {
		$docAttr = we_getTagAttribute("type", $attribs); // for Compatibility Reasons
	}
	$nameTo = we_getTagAttribute('nameto', $attribs);
	$to = we_getTagAttribute('to', $attribs, 'screen');

	switch ($docAttr) {
		case "top" :
			return we_redirect_tagoutput($GLOBALS["WE_MAIN_DOC"]->ID, $nameTo, $to);
		case "listview" :
			return we_redirect_tagoutput($GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count - 1], $nameTo, $to);
		case "self" :
		default :
			return we_redirect_tagoutput($GLOBALS["we_doc"]->ID, $nameTo, $to);
	}
}