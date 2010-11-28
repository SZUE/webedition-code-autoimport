<?php
function we_tag_navigationField($attribs, $content = ''){
	if (isset($GLOBALS['weNavigationItemArray']) && is_array($GLOBALS['weNavigationItemArray'])) {

		$element = $GLOBALS['weNavigationItemArray'][(sizeof($GLOBALS['weNavigationItemArray']) - 1)];
		return $element->getNavigationField($attribs);
	}
}?>
