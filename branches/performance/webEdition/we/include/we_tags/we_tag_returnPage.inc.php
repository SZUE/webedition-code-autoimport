<?php
function we_tag_returnPage($attribs, $content){

	$xml = we_getTagAttribute("xml", $attribs, "");

	return isset($_REQUEST["we_returnpage"]) ? (getXmlAttributeValueAsBoolean($xml) ? htmlspecialchars(
			$_REQUEST["we_returnpage"]) : $_REQUEST["we_returnpage"]) : "";
}?>
