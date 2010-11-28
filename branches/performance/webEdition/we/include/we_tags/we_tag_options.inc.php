<?php
function we_tag_options($attribs, $content){
	$name = we_getTagAttribute("name", $attribs);
	$classid = we_getTagAttribute("classid", $attribs);
	$field = we_getTagAttribute("field", $attribs);

	$o = "";
	if ($classid && $field) {
		if (!isset($GLOBALS["WE_OBJECT_DEFARRAY"])) {
			$GLOBALS["WE_OBJECT_DEFARRAY"] = array();
		}
		if (!isset($GLOBALS["WE_OBJECT_DEFARRAY"]["cid_$classid"])) {
			$db = new DB_WE();
			$GLOBALS["WE_OBJECT_DEFARRAY"]["cid_$classid"] = unserialize(
					f("SELECT DefaultValues FROM " . OBJECT_TABLE . " WHERE ID='$classid'", "DefaultValues", $db));
		}
		$foo = $GLOBALS["WE_OBJECT_DEFARRAY"]["cid_$classid"]["meta_$field"]["meta"];
		foreach ($foo as $key => $val) {
			$o .= '<option value="' . $key . '"' . ((($GLOBALS[$name] == $key) && strlen($GLOBALS[$name]) != 0) ? " selected" : "") . '>' . $val . '</option>' . "\n";
		}
		return $o;
	}
	return "";
}?>
