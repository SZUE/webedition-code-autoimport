<?php
/**
 * webEdition CMS
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

function we_tag_textarea($attribs, $content){
	include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/html/we_forms.inc.php");
	if (($foo = attributFehltError($attribs, "name", "textarea"))){
		return $foo;
	}
	
	$name = we_getTagAttribute("name", $attribs);
	$xml = we_getTagAttribute("xml", $attribs, "");
	$removeFirstParagraph = we_getTagAttribute("removefirstparagraph", $attribs, 0, true, defined("REMOVEFIRSTPARAGRAPH_DEFAULT") ? REMOVEFIRSTPARAGRAPH_DEFAULT : true);
	$attribs = removeAttribs($attribs, array('removefirstparagraph'));

	$html = we_getTagAttribute("html", $attribs, "", true, true);
	$autobrAttr = we_getTagAttribute("autobr", $attribs, "", true);
	$spellcheck = we_getTagAttribute('spellcheck', $attribs, 'true');

	$autobr = $GLOBALS["we_doc"]->getElement($name, "autobr");
	if (strlen($autobr) == 0) {
		$autobr = $autobrAttr ? "on" : "off";
	}
	$showAutobr = isset($attribs["autobr"]);
	if (!$showAutobr && $GLOBALS['we_editmode']) {
		$autobr = "off";
		$GLOBALS["we_doc"]->elements[$name]["autobr"] = "off";
		$GLOBALS["we_doc"]->saveInSession($_SESSION["we_data"][$GLOBALS['we_transaction']]);
	}

	$autobrName = 'we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $name . '#autobr]';
	$fieldname = 'we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $name . ']';
	$value = $GLOBALS["we_doc"]->getElement($name) ? $GLOBALS["we_doc"]->getElement($name) : $content;

	if ($GLOBALS['we_editmode']) {
		if((!$GLOBALS["we_doc"]->getElement($name)) && $value) { // when not inlineedit, we need to save the content in the object, if the field is empty
			$GLOBALS["we_doc"]->setElement($name, $value);
			$GLOBALS["we_doc"]->saveInSession($_SESSION["we_data"][$GLOBALS['we_transaction']]);
		}
		return we_forms::weTextarea(
				$fieldname,
				$value,
				$attribs,
				$autobr,
				$autobrName,
				$showAutobr,
				$GLOBALS["we_doc"]->getHttpPath(),
				false,
				false,
				getXmlAttributeValueAsBoolean($xml),
				$removeFirstParagraph,
				'',
				($spellcheck == 'true'));
	} else {
		return $GLOBALS["we_doc"]->getField($attribs);
	}
}
