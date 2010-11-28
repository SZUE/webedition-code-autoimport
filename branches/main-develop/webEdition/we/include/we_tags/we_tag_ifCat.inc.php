<?php
function we_tag_ifCat($attribs, $content){

	$categories = we_getTagAttribute("categories", $attribs);
	$category = we_getTagAttribute("category", $attribs);

	if (strlen($categories) == 0 && strlen($category) == 0) {
		$foo = attributFehltError($attribs, "categories", "ifCat");
		if ($foo) {
			print($foo);
			return "";
		}
	}

	$parent = we_getTagAttribute("parent", $attribs, "", true);

	$docAttr = we_getTagAttribute("doc", $attribs, "self");

	$match = $categories ? $categories : $category;
	$db = new DB_WE();
	$matchArray = makeArrayFromCSV($match);

	if ($docAttr == 'listview' && isset($GLOBALS['lv'])) {
		$DocCatsPaths = id_to_path($GLOBALS['lv']->f('wedoc_Category'), CATEGORY_TABLE, $db, true, false, $parent);
	} else {
		$doc = we_getDocForTag($docAttr);
		$DocCatsPaths = id_to_path($doc->Category, CATEGORY_TABLE, $db, true, false, $parent);
	}

	foreach ($matchArray as $match) {

		if (substr($match, 0, 1) != "/") {
			$match = "/" . $match;
		}
		if ($parent) {
			if (strpos($DocCatsPaths, ',' . $match . ',') !== false || strpos($DocCatsPaths, ',' . $match . '/') !== false) {
				return true;
			}
		} else {
			if (!(strpos($DocCatsPaths, "," . $match . ",") === false)) {
				return true;
			}
		}
	}
	return false;
}?>
