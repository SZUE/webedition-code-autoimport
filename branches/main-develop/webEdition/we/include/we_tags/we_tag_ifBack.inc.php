<?php
function we_tag_ifBack($attribs, $content){
	if (isset($GLOBALS['_we_voting_list']))
		return $GLOBALS['_we_voting_list']->hasPrevPage();
	$useparent = we_getTagAttribute("useparent", $attribs, '', true);
	return $GLOBALS["lv"]->hasPrevPage($useparent);
}?>
