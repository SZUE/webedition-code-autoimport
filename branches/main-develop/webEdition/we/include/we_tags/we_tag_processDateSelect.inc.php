<?php
function we_tag_processDateSelect($attribs, $content){
	$foo = attributFehltError($attribs, "name", "dateSelect");
	if ($foo)
		return $foo;
	$name = we_getTagAttribute("name", $attribs);
	$endofday = we_getTagAttribute("endofday", $attribs, "", true);
	$GLOBALS[$name] = $_REQUEST[$name] = mktime(
			$endofday ? 23 : 0,
			$endofday ? 59 : 0,
			$endofday ? 59 : 0,
			isset($_REQUEST[$name . "_month"]) ? $_REQUEST[$name . "_month"] : 0,
			isset($_REQUEST[$name . "_day"]) ? $_REQUEST[$name . "_day"] : 0,
			isset($_REQUEST[$name . "_year"]) ? $_REQUEST[$name . "_year"] : 0);
}?>
