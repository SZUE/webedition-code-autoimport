<?php
function we_tag_category($attribs, $content){

	// initialize possible Attributes
	$delimiter = we_getTagAttribute("delimiter", $attribs, "");
	if ($delimiter === "") {
		$delimiter = we_getTagAttribute("tokken", $attribs, "-");
	}

	$rootdir = we_getTagAttribute("rootdir", $attribs, "");
	$showpath = we_getTagAttribute("showpath", $attribs, false, true);
	$docAttr = we_getTagAttribute("doc", $attribs);
	$field = we_getTagAttribute("field", $attribs, "");
	$id = abs(we_getTagAttribute("id", $attribs));
	$separator = we_getTagAttribute("separator", $attribs, "/");
	$onlyindir = we_getTagAttribute("onlyindir", $attribs, "");

	// end initialize possible Attributes
	if ($id) {
		$category = str_replace(
				"\\,",
				",",
				we_getCatsFromIDs($id, $delimiter, $showpath, $GLOBALS["DB_WE"], $rootdir, $field, $onlyindir));
		return str_replace("/", $separator, $category);
	}

	$isInListview = isset($GLOBALS["lv"]) && (!$docAttr);

	if ($isInListview) {
		// get cats from listview object
		switch ($GLOBALS["lv"]->ClassName) {
			case "we_listview_object" :
				$catIDs = $GLOBALS["lv"]->f("wedoc_Category");
				break;
			case "we_search_listview" :
				$catIDs = $GLOBALS["lv"]->f("wedoc_Category");
				break;
			default :
				$catIDs = $GLOBALS["lv"]->f("wedoc_Category");
		}

		$category = $catIDs ? str_replace(
				"\\,",
				",",
				we_getCatsFromIDs($catIDs, $delimiter, $showpath, $GLOBALS["DB_WE"], $rootdir, $field, $onlyindir)) : "";
		return str_replace("/", $separator, $category);

	} else {
		$doc = we_getDocForTag($docAttr, false);
		$category = str_replace(
				"\\,",
				",",
				we_getCatsFromDoc($doc, $delimiter, $showpath, $GLOBALS["DB_WE"], $rootdir, $field, $onlyindir));
		return str_replace("/", $separator, $category);
	}
}?>
