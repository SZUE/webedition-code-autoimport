<?php
function we_tag_ifSelf($attribs, $content){

	$id = we_getTagAttribute("id", $attribs);

	if (!$id) {
		if (isset($GLOBALS["we_obj"])) {
			$id = $GLOBALS["we_obj"]->ID;
		} else {
			$id = $GLOBALS["WE_MAIN_DOC"]->ID;
		}
	}
	$type = we_getTagAttribute("doc", $attribs);
	$type = $type ? $type : we_getTagAttribute("type", $attribs);

	$ids = makeArrayFromCSV($id);

	switch ($type) {
		case "listview" :
			if ($GLOBALS["lv"]->ClassName == "we_listview_object") {
				return in_array($GLOBALS["lv"]->DB_WE->f("OF_ID"), $ids);
			} else
				if ($GLOBALS["lv"]->ClassName == "we_search_listview") {
					return in_array($GLOBALS["lv"]->DB_WE->f("WE_ID"), $ids);
				} else
					if ($GLOBALS["lv"]->ClassName == "we_listview_shopVariants") {
						reset($GLOBALS['lv']->Record);
						$key = key($GLOBALS['lv']->Record);
						if (isset($GLOBALS['we_doc']->Variant)) {

							if ($key == $GLOBALS['we_doc']->Variant) {
								return true;
							}
						} else {
							if ($key == $GLOBALS['lv']->DefaultName) {
								return true;
							}
						}
						return false;
					} else {
						return in_array($GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count - 1], $ids);
					}
		case "self" :
			return in_array($GLOBALS["we_doc"]->ID, $ids);
		default :
			return in_array($GLOBALS["WE_MAIN_DOC"]->ID, $ids);
	}
}?>
