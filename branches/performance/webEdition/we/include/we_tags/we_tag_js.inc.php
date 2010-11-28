<?php
function we_tag_js($attribs, $content){

	$foo = attributFehltError($attribs, "id", "js");
	if ($foo)
		return $foo;
	$id = we_getTagAttribute("id", $attribs);
	$row = getHash("SELECT Path,IsFolder,IsDynamic FROM " . FILE_TABLE . " WHERE ID=".abs($id)."", new DB_WE());

	if (count($row)) {

		$url = $row["Path"] . ($row["IsFolder"] ? "/" : "");

		$attribs["type"] = "text/javascript";
		$attribs["src"] = $url;

		$attribs = removeAttribs($attribs, array(
			"id"
		));

		//	prepare $attribs for output:
		return getHtmlTag("script", $attribs, "", true) . "\n";

	}
	return "";
}?>
