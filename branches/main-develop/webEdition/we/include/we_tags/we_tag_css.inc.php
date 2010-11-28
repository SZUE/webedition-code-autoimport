<?php
function we_tag_css($attribs, $content){

	$foo = attributFehltError($attribs, "id", "css");
	if ($foo)
		return $foo;
	$id = we_getTagAttribute("id", $attribs);
	$rel = we_getTagAttribute("rel", $attribs, "stylesheet");

	$row = getHash("SELECT Path,IsFolder,IsDynamic FROM " . FILE_TABLE . " WHERE ID=".abs($id)."", new DB_WE());
	if (count($row)) {
		$url = $row["Path"] . ($row["IsFolder"] ? "/" : "");

		//	remove not needed elements
		$attribs = removeAttribs($attribs, array(
			"id", "rel"
		));

		$attribs["rel"] = $rel;
		$attribs["type"] = "text/css";
		$attribs["href"] = $url;

		return getHtmlTag("link", $attribs);
	}
	return "";
}?>
