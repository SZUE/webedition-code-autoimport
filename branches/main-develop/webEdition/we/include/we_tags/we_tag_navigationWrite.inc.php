<?php
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tools/navigation/class/weNavigationItems.class.php');

function we_tag_navigationWrite($attribs, $content = ''){

	$name = we_getTagAttribute("navigationname", $attribs, "default");
	$depth = we_getTagAttribute("depth", $attribs);

	if (!$depth) {
		$depth = false;
	}

	if (isset($GLOBALS['we_navigation'][$name])) {

		$GLOBALS['weNavigationDepth'] = $depth;
		print $GLOBALS['we_navigation'][$name]->writeNavigation($depth);
		unset($GLOBALS['weNavigationDepth']);
	}
}?>
