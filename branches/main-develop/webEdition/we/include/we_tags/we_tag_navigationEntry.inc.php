<?php
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tools/navigation/class/weNavigationItems.class.php');

function we_tag_navigationEntry($attribs, $content = ''){

	$foo = attributFehltError($attribs, 'type', 'navigation');
	if ($foo)
		return $foo;

	$navigationName = we_getTagAttribute('navigationname', $attribs, "default");
	$type = we_getTagAttribute('type', $attribs);
	$level = we_getTagAttribute('level', $attribs, 'defaultLevel');
	$current = we_getTagAttribute('current', $attribs, 'defaultCurrent');
	$position = we_getTagAttribute('position', $attribs, 'defaultPosition');

	$tp = new we_tagParser();
	$tags = $tp->getAllTags($content);

	$tp->parseTags($tags, $content);

	$_positions = makeArrayFromCSV($position);

	for ($i = 0; $i < sizeof($_positions); $i++) {
		$position = $_positions[$i];
		if ($position == 'first') {
			$position = 1;
		}
		$GLOBALS['we_navigation'][$navigationName]->setTemplate($content, $type, $level, $current, $position);
	}
}?>
