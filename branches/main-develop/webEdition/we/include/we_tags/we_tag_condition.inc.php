<?php
function we_tag_condition($attribs, $content){

	$name = we_getTagAttribute("name", $attribs, "we_lv_condition");

	$GLOBALS["we_lv_conditionCount"] = isset($GLOBALS["we_lv_conditionCount"]) ? abs($GLOBALS["we_lv_conditionCount"]) : 0;

	if ($GLOBALS["we_lv_conditionCount"] == 0) {
		$GLOBALS["we_lv_conditionName"] = $name;
		$GLOBALS[$GLOBALS["we_lv_conditionName"]] = "(";
	} else {
		$GLOBALS[$GLOBALS["we_lv_conditionName"]] .= "(";
	}
	$GLOBALS["we_lv_conditionCount"]++;
	return "";
}

function we_tag_conditionAdd($attribs, $content){

	$foo = attributFehltError($attribs, "field", "conditionAdd");
	if ($foo)
		return $foo;

	// initialize possible Attributes
	$field = we_getTagAttribute("field", $attribs);
	$value = we_getTagAttribute("value", $attribs);
	$compare = we_getTagAttribute("compare", $attribs, "=");
	$var = we_getTagAttribute("var", $attribs);
	$type = we_getTagAttribute("type", $attribs);
	$property = we_getTagAttribute("property", $attribs, "", true);
	$docAttr = we_getTagAttribute("doc", $attribs);
	// end initialize possible Attributes


	$value = str_replace('&gt;', '>', $value);
	$value = str_replace('&lt;', '<', $value);

	$regs = array();
	if ($var && $compare == "like") {
		if (ereg('^(%)?([^%]+)(%)?$', $var, $regs)) {
			$var = $regs[2];
		}
	}
	switch (strtolower($type)) {
		case "now" :
			$value = time();
		case "sessionfield" :
			if ($var && isset($_SESSION["webuser"][$var])) {
				$value = $_SESSION["webuser"][$var];
			}
			break;
		case "document" :
			if ($var) {
				$doc = we_getDocForTag($docAttr, false);
				if ($property) {
					eval('$value = $doc->' . $var . ';');
				} else {
					$value = $doc->getElement($var);
				}
			}
			break;
		case "request" :
			if ($var && isset($_REQUEST[$var])) {
				$value = $_REQUEST[$var];
			}
			break;
		default :
			if ($var && isset($GLOBALS[$var])) {
				$value = $GLOBALS[$var];
			}
	}

	$value = (isset($regs[1]) ? $regs[1] : "") . $value . (isset($regs[3]) ? $regs[3] : "");

	if (strlen($field) && isset($GLOBALS["we_lv_conditionName"]) && isset($GLOBALS[$GLOBALS["we_lv_conditionName"]])) {
		$GLOBALS[$GLOBALS["we_lv_conditionName"]] .= " ($field $compare '" . addslashes($value) . "') ";
	} else {
		if (eregi('^(.*)AND ?$', $GLOBALS[$GLOBALS["we_lv_conditionName"]])) {
			$GLOBALS[$GLOBALS["we_lv_conditionName"]] .= "1 ";
		} else {
			$GLOBALS[$GLOBALS["we_lv_conditionName"]] .= "0 ";
		}
	}
	return "";
}

function we_tag_conditionAND($attribs, $content){
	if (isset($GLOBALS["we_lv_conditionName"]) && isset($GLOBALS[$GLOBALS["we_lv_conditionName"]])) {
		$GLOBALS[$GLOBALS["we_lv_conditionName"]] .= " AND ";
	}
	return "";
}

function we_tag_conditionOR($attribs, $content){
	if (isset($GLOBALS["we_lv_conditionName"]) && isset($GLOBALS[$GLOBALS["we_lv_conditionName"]])) {
		$GLOBALS[$GLOBALS["we_lv_conditionName"]] .= " OR ";
	}
	return "";
}?>
