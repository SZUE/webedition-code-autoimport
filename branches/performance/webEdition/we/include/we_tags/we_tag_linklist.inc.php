<?php
function we_tag_linklist($attribs, $content){
	include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/" . "we_linklist.inc.php");
	$name = we_getTagAttribute("name", $attribs);
	$content = str_replace("we:link", "we_:_link", $content);
	$foo = attributFehltError($attribs, "name", "linklist");
	if ($foo)
		return $foo;
	$isInListview = isset($GLOBALS["lv"]);

	if ($isInListview) {
		$linklist = $GLOBALS["lv"]->f($name);
	} else
		if (isset($GLOBALS["we_doc"])) {
			$linklist = $GLOBALS["we_doc"]->getElement($name);
		}
	$ll = new we_linklist($linklist);
	$ll->name = $name;

	$out = $ll->getHTML(
			(isset($GLOBALS["we_editmode"]) && $GLOBALS["we_editmode"] && (!$isInListview)),
			$attribs,
			$content,
			$GLOBALS["we_doc"]->Name);
	return $out;
}?>
