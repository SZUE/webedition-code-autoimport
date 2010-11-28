<?php
function we_tag_ifVarSet($attribs, $content){
	$foo = attributFehltError($attribs, "name", "ifVarSet");
	if ($foo) {
		print($foo);
		return "";
	}

	$type = we_getTagAttribute("var", $attribs);
	$type = $type ? $type : we_getTagAttribute("type", $attribs);
	$doc = we_getTagAttribute("doc", $attribs);
	$name = we_getTagAttribute("name", $attribs);
	$formname = we_getTagAttribute("formname", $attribs, "we_global_form");
	$property = we_getTagAttribute("property", $attribs, "", true);
	$shopname = we_getTagAttribute('shopname', $attribs, '');

	return we_isVarSet($name, $type, $doc, $property, $formname, $shopname);
}
?>
