<?php
function we_tag_ifHasChildren($attribs, $content){

	if (isset($GLOBALS["lv"])) {
		if (abs($GLOBALS["lv"]->f("ID")) > 0) {
			return abs(
					f(
							"SELECT COUNT(ID) AS ID FROM " . CATEGORY_TABLE . " WHERE ParentID='" . abs(
									$GLOBALS["lv"]->f("ID")) . "'",
							"ID",
							new DB_WE())) > 0;
		}
	}
	return false;
}?>
