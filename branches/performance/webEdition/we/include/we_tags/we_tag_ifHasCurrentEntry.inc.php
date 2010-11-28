<?php
function we_tag_ifHasCurrentEntry($attribs = array(), $content = ''){
	if (isset($GLOBALS['weNavigationItemArray']) && is_array($GLOBALS['weNavigationItemArray'])) {

		$element = $GLOBALS['weNavigationItemArray'][(sizeof($GLOBALS['weNavigationItemArray']) - 1)];

		if (sizeof($element->items)) {
			foreach ($element->items as $key => $value) {
				if ($value->containsCurrent == 'true') {
					return true;
				}
			}
		}
		return false;
	}
}?>
