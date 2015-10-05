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
function we_tag_textarea($attribs, $content){
	if(($foo = attributFehltError($attribs, "name", __FUNCTION__))){
		return $foo;
	}

	$name = weTag_getAttribute("name", $attribs, '', we_base_request::STRING);
	$xml = weTag_getAttribute("xml", $attribs, XHTML_DEFAULT, we_base_request::BOOL);
	$removeFirstParagraph = weTag_getAttribute("removefirstparagraph", $attribs, defined('REMOVEFIRSTPARAGRAPH_DEFAULT') ? REMOVEFIRSTPARAGRAPH_DEFAULT : true, we_base_request::BOOL);
	$attribs = removeAttribs($attribs, array('removefirstparagraph'));

	$html = weTag_getAttribute("html", $attribs, true, we_base_request::BOOL);
	$autobrAttr = weTag_getAttribute("autobr", $attribs, false, we_base_request::BOOL);
	$spellcheck = weTag_getAttribute('spellcheck', $attribs, true, we_base_request::BOOL);

	$autobr = $GLOBALS['we_doc']->getElement($name, "autobr");
	if(strlen($autobr) == 0){
		$autobr = $autobrAttr ? "on" : "off";
	}
	$showAutobr = isset($attribs["autobr"]);
	if(!$showAutobr && $GLOBALS['we_editmode']){
		$autobr = "off";
		$GLOBALS['we_doc']->elements[$name]["autobr"] = "off";
		$GLOBALS['we_doc']->saveInSession($_SESSION['weS']['we_data'][$GLOBALS['we_transaction']]);
	}

	$autobrName = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . '#autobr]';
	$fieldname = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']';
	$value = $GLOBALS['we_doc']->getElement($name) ? : $content;

	if($GLOBALS['we_editmode']){
		if((!$GLOBALS['we_doc']->getElement($name)) && $value){ // when not inlineedit, we need to save the content in the object, if the field is empty
			$GLOBALS['we_doc']->setElement($name, $value);
			$GLOBALS['we_doc']->saveInSession($_SESSION['weS']['we_data'][$GLOBALS['we_transaction']]);
		}
		return we_html_forms::weTextarea($fieldname, $value, $attribs, $autobr, $autobrName, $showAutobr, $GLOBALS['we_doc']->getHttpPath(), false, false, $xml, $removeFirstParagraph, '', ($spellcheck == 'true'), false, $name);
	}

	$fieldVal = we_document::parseInternalLinks($GLOBALS['we_doc']->getField($attribs), 0, '');
	if(!weTag_getAttribute('wysiwyg', $attribs, false, we_base_request::BOOL) || strpos($fieldVal, '</wegallery>') === false){
		return $fieldVal;
	}

	/* we are in wysiwyg and have at least one wegallery */
	$galleryAttribs = $regs = array();
	if(preg_match_all('/<wegallery *((id|tmpl)="\d+")* *((id|tmpl)="\d+")* *><\/wegallery>/i', $fieldVal, $regs, PREG_SET_ORDER)){
		for($i = 0; $i < count($regs); $i++){
			array_shift($regs[$i]);
			foreach($regs[$i] as $reg){
				if(($pos = strpos($reg, '=')) !== false){
					$galleryAttribs[$i][substr($reg, 0, $pos)] = substr($reg, $pos + 2, -1);
				}
			}
		}
	}

	$splitVal = preg_split('/<wegallery *((id|tmpl)="\d+")* *((id|tmpl)="\d+")* *><\/wegallery>/i', $fieldVal);
	printElement(array_shift($splitVal));
	foreach($splitVal as $i => $cur){
		if($galleryAttribs[$i]['id'] && $galleryAttribs[$i]['tmpl']){
			$GLOBALS['WE_COLLECTION_ID'] = $galleryAttribs[$i]['id'];
			if(($we_inc = we_tag('include', array('type' => 'template', 'id' => intval($galleryAttribs[$i]['tmpl']), '_parsed' => true)))){
				include($we_inc);
			}
			unset($GLOBALS['WE_COLLECTION_ID']);
		}
		printElement($cur);
	}

	return;

	/*
	  $fieldVal = array_shift($splitVal);
	  for($i = 0; $i < count($splitVal); $i++){
	  if($galleryAttribs[$i]['id'] && $galleryAttribs[$i]['tmpl']){t_e("ga", $galleryAttribs[$i]['id'], $galleryAttribs[$i]['tmpl']);
	  $GLOBALS['WE_COLLECTION_ID'] = $galleryAttribs[$i]['id'];
	  ob_start();
	  if(($we_inc = we_tag('include', array('type' => 'template', 'id' => intval($galleryAttribs[$i]['tmpl']), '_parsed' => true)))){
	  include($we_inc);
	  }
	  $fieldVal .= ob_get_clean();
	  }
	  $fieldVal .= $splitVal[$i];
	  }

	  return $fieldVal;
	 *
	 */
}
