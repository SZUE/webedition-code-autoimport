<?php
function we_tag_navigationEntries($attribs, $content = ''){
	if (isset($GLOBALS['weNavigationItemArray']) && is_array($GLOBALS['weNavigationItemArray'])) {
		$element = $GLOBALS['weNavigationItemArray'][(sizeof($GLOBALS['weNavigationItemArray']) - 1)];
		$code = '';

		foreach ($element->items as $item) {
			$code .= $item->writeItem($GLOBALS['weNavigationObject'], $GLOBALS['weNavigationDepth']);
		}

		return $code;
	}
}?>
