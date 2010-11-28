<?php
function we_tag_icon($attribs, $content){
	$foo = attributFehltError($attribs, "id", "a");
	if ($foo)
		return $foo;
	$xml = we_getTagAttribute("xml", $attribs, "");
	$id = we_getTagAttribute("id", $attribs);
	$row = getHash("SELECT Path,IsFolder,IsDynamic FROM " . FILE_TABLE . " WHERE ID=".abs($id)."", new DB_WE());
	if (count($row)) {
		$url = $row["Path"] . ($row["IsFolder"] ? "/" : "");
		return getHtmlTag('link', array(
			'rel' => 'shortcut icon', 'href' => $url, 'xml' => $xml
		));
	}
	return "";
}?>
