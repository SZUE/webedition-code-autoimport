<?php
function we_tag_position($attribs, $content){

	global $lv;

	//	type is required !!!
	$missingAttrib = attributFehltError($attribs, "type", "position");
	if ($missingAttrib) {
		print $missingAttrib;
		return "";
	}

	//	here we get the needed attributes
	$type = we_getTagAttribute("type", $attribs);
	$_reference = we_getTagAttribute("reference", $attribs);
	$format = we_getTagAttribute("format", $attribs, 1);

	//	this value we will return later
	$_retPos = "";

	switch ($type) {

		case "listview" : //	inside a listview, we take direct global listview object
			$_retPos = ($lv->start + $lv->count);
			break;

		case "listdir" : //	inside a listview
			if (isset($GLOBALS['we_position']['listdir'])) {
				$_content = $GLOBALS['we_position']['listdir'];
			}
			if (isset($_content) && $_content['position']) {
				$_retPos = $_content['position'];
			}
			break;

		case "linklist" : //	look in fkt we_tag_linklist and class we_linklist for details
		case "block" : //	look in function we_tag_block for details
			//	first we must get right array !!!
			$missingAttrib = attributFehltError($attribs, "reference", "position");
			if ($missingAttrib) {
				print $missingAttrib;
				return "";
			}
			foreach ($GLOBALS['we_position'][$type] as $name => $arr) {

				if (strpos($name, $_reference) === 0) {
					if (is_array($arr)) {
						$_content = $arr;
					}
				}
			}
			if (isset($_content) && $_content['position']) {
				$_retPos = $_content['position'];
			}
			break;
	}

	//	convert to desired format
	switch ($format) {

		case "a" :
			return number2System($_retPos);
			break;

		case "A" :
			return strtoupper(number2System($_retPos));
			break;

		default :
			return $_retPos;
			break;
	}
}?>
