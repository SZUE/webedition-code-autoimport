<?php
function we_tag_ifHasEntries($attribs = array(), $content = ''){
	if (isset($GLOBALS['weNavigationItemArray']) && is_array($GLOBALS['weNavigationItemArray'])) {

		$element = $GLOBALS['weNavigationItemArray'][(sizeof($GLOBALS['weNavigationItemArray']) - 1)];

		if (sizeof($element->items)) {
			return true;
		}
		return false;
	}
}?>
