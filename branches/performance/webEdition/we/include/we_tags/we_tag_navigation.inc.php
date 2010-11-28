<?php
	include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tools/navigation/class/weNavigationItems.class.php');

function we_tag_navigation($attribs, $content = ''){
	$parentid = we_getTagAttribute("parentid", $attribs, -1);
	$id = we_getTagAttribute("id", $attribs, 0);
	$name = we_getTagAttribute("navigationname", $attribs, "default");

	if (isset($GLOBALS['initNavigationFromSession']) && $GLOBALS['initNavigationFromSession']) {

		$GLOBALS['we_navigation'][$name] = new weNavigationItems();
		$GLOBALS['we_navigation'][$name]->initByNavigationObject($parentid == -1 ? true : false);

	} else {

		$GLOBALS['we_navigation'][$name] = new weNavigationItems();

		if (!$GLOBALS['we_navigation'][$name]->initFromCache(($id ? $id : ($parentid != -1 ? $parentid : 0)), false)) {

			$GLOBALS['we_navigation'][$name]->initById(
					$id ? $id : ($parentid != -1 ? $parentid : 0),
					false,
					$id ? true : ($parentid != -1 ? false : true));
		}

	}
}
?>
