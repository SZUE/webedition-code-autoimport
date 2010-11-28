<?php
function we_tag_description($attribs, $content){
	global $DESCRIPTION;
	$htmlspecialchars = we_getTagAttribute("htmlspecialchars", $attribs, "", true);
	$attribs = removeAttribs($attribs, array(
		'htmlspecialchars'
	));

	if ($GLOBALS["we_doc"]->EditPageNr == WE_EDITPAGE_PROPERTIES && $GLOBALS["we_doc"]->InWebEdition) { //	normally meta tags are edited on property page


		return '<?php	$GLOBALS["meta"]["Description"]["default"] = "' . str_replace('"', '\"', $content) . '"; ?>';
	} else {

		$descr = $DESCRIPTION ? $DESCRIPTION : $content;

		$attribs["name"] = "description";
		$attribs["content"] = $htmlspecialchars ? htmlspecialchars(strip_tags($descr)) : strip_tags($descr);

		return getHtmlTag("meta", $attribs) . "\n";
	}
}?>
