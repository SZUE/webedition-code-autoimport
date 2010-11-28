<?php
function we_tag_hidden($attribs, $content){

	$foo = attributFehltError($attribs, "name", "hidden");
	if ($foo)
		return $foo;

	$name = we_getTagAttribute("name", $attribs);
	$type = we_getTagAttribute("type", $attribs, '');
	$xml = we_getTagAttribute("xml", $attribs);

	$value = '';
	switch ($type) {
		case 'session' :

			$value = $_SESSION[$name];
			break;
		case 'request' :
			$value = removePHP(isset($_REQUEST[$name]) ? $_REQUEST[$name] : "");
			break;
		default :
			$value = $GLOBALS[$name];
			break;
	}

	return getHtmlTag('input', array(
		'type' => 'hidden', 'name' => $name, 'value' => $value, 'xml' => $xml
	));
}?>
