<?php

/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function we_tag_textarea(array $attribs, $content){
	if(($foo = attributFehltError($attribs, "name", __FUNCTION__))){
		return $foo;
	}

	if($GLOBALS['we_editmode']){
		$name = weTag_getAttribute("name", $attribs, '', we_base_request::STRING);
		$xml = weTag_getAttribute("xml", $attribs, XHTML_DEFAULT, we_base_request::BOOL);
		$spellcheck = weTag_getAttribute('spellcheck', $attribs, true, we_base_request::BOOL);

		$removeFirstParagraph = weTag_getAttribute("removefirstparagraph", $attribs, defined('REMOVEFIRSTPARAGRAPH_DEFAULT') ? REMOVEFIRSTPARAGRAPH_DEFAULT : true, we_base_request::BOOL);
		$autobrAttr = weTag_getAttribute("autobr", $attribs, false, we_base_request::BOOL);
		$autobr = $GLOBALS['we_doc']->getElement($name, "autobr")? : ($autobrAttr ? "on" : "off");
		$showAutobr = isset($attribs["autobr"]);
		if(!$showAutobr){
			$autobr = 'off';
			$GLOBALS['we_doc']->elements[$name]["autobr"] = "off";
			$GLOBALS['we_doc']->saveInSession($_SESSION['weS']['we_data'][$GLOBALS['we_transaction']]);
		}
		$value = $GLOBALS['we_doc']->getElement($name) ? : $content;
		if((!$GLOBALS['we_doc']->getElement($name)) && $value){ // when not inlineedit, we need to save the content in the object, if the field is empty
			$GLOBALS['we_doc']->setElement($name, $value);
			$GLOBALS['we_doc']->saveInSession($_SESSION['weS']['we_data'][$GLOBALS['we_transaction']]);
		}
		return we_html_forms::weTextarea('we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']', $value, $attribs, $autobr, 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . '#autobr]', $showAutobr, $GLOBALS['we_doc']->getHttpPath(), false, false, $xml, $removeFirstParagraph, '', $spellcheck, false, $name);
	}

	$fieldVal = we_document::parseInternalLinks($GLOBALS['we_doc']->getField($attribs), 0, '');
	if(!strpos($fieldVal, '</we-gallery>')){
		return $fieldVal;
	}

	$gallery = false;
	/* we are in wysiwyg and have at least one we-gallery */
	$splitVal = preg_split('&(<we-gallery)([^>]*)></we-gallery>&i', $fieldVal, -1, PREG_SPLIT_DELIM_CAPTURE);
	ob_start();
	foreach($splitVal as $split){
		if($split === '<we-gallery'){
			$gallery = true;
			continue;
		}
		if($gallery){
			$gallery = false;
			$galleryAttribs = we_tag_tagParser::parseAttribs($split, true);
			if($galleryAttribs['id'] && $galleryAttribs['tmpl']){
				$GLOBALS['WE_COLLECTION_ID'] = $galleryAttribs['id'];
				if(($we_inc = we_tag('include', array('type' => 'template', 'id' => intval($galleryAttribs['tmpl']), '_parsed' => true)))){
					include($we_inc);
				}
				unset($GLOBALS['WE_COLLECTION_ID']);
			}
			continue;
		}
		printElement($split);
	}

	return ob_get_clean();
}
