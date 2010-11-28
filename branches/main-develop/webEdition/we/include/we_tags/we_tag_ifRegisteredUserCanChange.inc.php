<?php
function we_tag_ifRegisteredUserCanChange($attribs, $content){
	$admin = we_getTagAttribute("admin", $attribs);
	$userid = we_getTagAttribute("userid", $attribs); // deprecated  use protected=true instead
	$protected = we_getTagAttribute("protected", $attribs, "", true);
	if (!(isset($_SESSION["webuser"]) && isset($_SESSION["webuser"]["ID"]))) {
		return false;
	}
	if ($admin) {
		if (isset($_SESSION["webuser"][$admin]) && $_SESSION["webuser"][$admin])
			return true;
	}

	$listview = isset($GLOBALS["lv"]);

	if ($listview) {
		if ($protected) {
			return $GLOBALS["lv"]->f("wedoc_WebUserID") == $_SESSION["webuser"]["ID"];
		} else {
			return $GLOBALS["lv"]->f($userid) == $_SESSION["webuser"]["ID"];
		}
	} else {
		if ($protected) {
			return $GLOBALS["we_doc"]->WebUserID == $_SESSION["webuser"]["ID"];
		} else {
			return $GLOBALS["we_doc"]->getElement($userid) == $_SESSION["webuser"]["ID"];
		}
	}
}?>
