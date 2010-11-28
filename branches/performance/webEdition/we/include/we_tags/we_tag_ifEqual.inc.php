<?php
function we_tag_ifEqual($attribs, $content){
	global $we_editmode;
	$foo = attributFehltError($attribs, "name", "ifEqual");
	if ($foo) {
		print($foo);
		return "";
	}
	$name = we_getTagAttribute("name", $attribs);
	$eqname = we_getTagAttribute("eqname", $attribs);
	$value = we_getTagAttribute("value", $attribs);

	if (!$eqname) {
		$foo = attributFehltError($attribs, "value", "ifEqual");
		if ($foo) {
			print($foo);
			return "";
		}
		return ($GLOBALS["we_doc"]->getElement($name) == $value);
	}

	$foo = attributFehltError($attribs, "eqname", "ifEqual");
	if ($foo) {
		print($foo);
		return "";
	}
	if ($GLOBALS["we_doc"]->getElement($name) && $GLOBALS["WE_MAIN_DOC"]->getElement($eqname)) {
		return ($GLOBALS["we_doc"]->getElement($name) == $GLOBALS["WE_MAIN_DOC"]->getElement($eqname));
	} else {
		if (isset($GLOBALS[$eqname])) {
			return $GLOBALS[$eqname] == $GLOBALS["we_doc"]->getElement($name);
		} else {
			return false;
		}
	}

}?>
