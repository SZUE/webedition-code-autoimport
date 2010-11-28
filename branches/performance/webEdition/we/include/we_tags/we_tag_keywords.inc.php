<?php
function we_tag_keywords($attribs, $content){
	$htmlspecialchars = we_getTagAttribute("htmlspecialchars", $attribs, "", true);
	$attribs = removeAttribs($attribs, array(
		'htmlspecialchars'
	));

	if ($GLOBALS["we_doc"]->EditPageNr == WE_EDITPAGE_PROPERTIES && $GLOBALS["we_doc"]->InWebEdition) { //	normally meta tags are edited on property page


		return '<?php	$GLOBALS["meta"]["Keywords"]["default"] = "' . str_replace('"', '\"', $content) . '"; ?>';
	}
	$keys = $GLOBALS['KEYWORDS'] ? $GLOBALS['KEYWORDS'] : $content;

	$attribs["name"] = "keywords";
	$attribs["content"] = $htmlspecialchars ? htmlspecialchars(strip_tags($keys)) : strip_tags($keys);
	return getHtmlTag("meta", $attribs) . "\n";
}?>
