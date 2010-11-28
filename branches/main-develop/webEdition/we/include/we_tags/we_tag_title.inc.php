<?php
function we_tag_title($attribs, $content){
	global $TITLE;
	$htmlspecialchars = we_getTagAttribute("htmlspecialchars", $attribs, "", true);
	$attribs = removeAttribs($attribs, array(
		'htmlspecialchars'
	));
	if ($GLOBALS["we_doc"]->EditPageNr == WE_EDITPAGE_PROPERTIES && $GLOBALS["we_doc"]->InWebEdition) { //	normally meta tags are edited on property page


		return '<?php	$GLOBALS["meta"]["Title"]["default"] = "' . str_replace('"', '\"', $content) . '"; ?>';
	} else {

		$title = $TITLE ? $TITLE : $content;
		return getHtmlTag(
				"title",
				$attribs,
				$htmlspecialchars ? htmlspecialchars(strip_tags($title)) : strip_tags($title),
				true) . "\n";
	}
}?>
