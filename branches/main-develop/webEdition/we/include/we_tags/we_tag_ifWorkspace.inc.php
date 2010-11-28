<?php
function we_tag_ifWorkspace($attribs, $content){
	$required_path = we_getTagAttribute('path', $attribs, "");
	$docAttr = we_getTagAttribute("doc", $attribs, "self");
	$doc = we_getDocForTag($docAttr);
	$id = we_getTagAttribute('id', $attribs);

	if (!$required_path) {
		$required_path = id_to_path($id);
	}

	if (!$required_path) {
		return false;
	}

	if (substr($required_path, 0, 1) != '/') {
		$required_path = '/' . $required_path;
	}

	return (strpos($doc->Path, $required_path) === 0);
}?>
