<?php
function we_tag_back($attribs, $content){
	if (isset($GLOBALS["_we_voting_list"]))
		return $GLOBALS["_we_voting_list"]->getBackLink($attribs);
	else
		return $GLOBALS["lv"]->getBackLink($attribs);
}?>
