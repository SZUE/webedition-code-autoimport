<?php
function we_tag_author($attribs, $content){
	// attributes
	$type = we_getTagAttribute("type", $attribs);
	$creator = we_getTagAttribute("creator", $attribs, '', true);
	$docAttr = we_getTagAttribute("doc", $attribs);

	$doc = we_getDocForTag($docAttr, true);

	$foo = getHash(
			"SELECT Username,First,Second FROM " . USER_TABLE . " WHERE ID='" . abs($creator ? $doc->CreatorID : $doc->ModifierID) . "'",
			new DB_WE());

	switch ($type) {
		case "name" :
			$out = trim(($foo["First"] ? ($foo["First"] . " ") : "") . $foo["Second"]);
			if (!$out) {
				$out = $foo["Username"];
			}
			return $out;
		case "initials" :
			$out = trim(
					($foo["First"] ? substr($foo["First"], 0, 1) : "") . ($foo["Second"] ? substr(
							$foo["Second"],
							0,
							1) : ""));
			if (!$out) {
				$out = $foo["Username"];
			}
			return $out;
		default :
			return $foo["Username"];
	}
}?>
