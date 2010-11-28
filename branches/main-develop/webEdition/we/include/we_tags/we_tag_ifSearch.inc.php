<?php
function we_tag_ifSearch($attribs, $content){
	$name = we_getTagAttribute("name", $attribs, "0");
	$set = we_getTagAttribute("set", $attribs, 1, true);

	if ($set) {
		return isset($_REQUEST["we_lv_search_" . $name]);
	} else {
		return isset($_REQUEST["we_lv_search_" . $name]) && strlen(
				str_replace(
						"\"",
						"",
						str_replace(
								"\\\"",
								"",
								(isset($_REQUEST["we_lv_search_" . $name]) ? trim(
										$_REQUEST["we_lv_search_" . $name]) : ""))));
	}
}?>
