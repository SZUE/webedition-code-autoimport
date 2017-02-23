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
require_once(WE_INCLUDES_PATH . 'we_tag.inc.php');

we_html_tools::protect();

$charset = (!empty($GLOBALS['we_doc']->Charset) ? //	send charset which might be determined in template
		$GLOBALS['we_doc']->Charset : DEFAULT_CHARSET);

//we_html_tools::headerCtCharset('text/html', $charset);

$editMode = (isset($previewMode) && $previewMode == 1 ? 0 : 1);
$parts = $GLOBALS['we_doc']->getFieldsHTML($editMode);
if(is_array($GLOBALS['we_doc']->DefArray)){
	foreach($GLOBALS['we_doc']->DefArray as $n => $v){
		if(is_array($v)){
			if(!empty($v["required"]) && $editMode){
				$parts[] = ["headline" => "",
					"html" => '*' . g_l('global', '[required_fields]'),
					"name" => str_replace('.', '', uniqid('', true)),
				];
				break;
			}
		}
	}
}
$weSuggest = &weSuggest::getInstance();

$jsCmd = new we_base_jsCmd();

$head = '';
if($GLOBALS['we_doc']->CSS){
	$cssArr = makeArrayFromCSV($GLOBALS['we_doc']->CSS);
	foreach($cssArr as $cs){
		$head .= we_html_element::cssLink(id_to_path($cs));
	}
}

$content = '';
if($editMode){
	$content .= we_html_multiIconBox::_getBoxStart(g_l('weClass', '[edit]'), md5(uniqid(__FILE__, true)), 30) .
		'<div id="orderContainer"></div>' .
		we_html_multiIconBox::_getBoxEnd();
	foreach($parts as $part){
		$content .= '<div id="' . $part['name'] . '" class="objectFileElement"><div id="f' . $part['name'] . '" class="default defaultfont">' . $part["html"] . '</div></div>';
		$jsCmd->addCmd('orderContainerAdd', $part['name']);
	}
} else {
	$content .= we_SEEM::parseDocument(we_html_multiIconBox::getHTML('', $parts, 30));
}

echo we_html_tools::getHtmlTop('', $charset, 5, $head . we_html_element::jsScript(JS_DIR . '/weOrderContainer.js') .
	we_html_element::jsScript(JS_DIR . 'multiIconBox.js') .
	we_editor_script::get() .
	$jsCmd->getCmds(), we_html_element::htmlBody([
		'onunload' => "doUnload()",
		'class' => "weEditorBody",
		'onload' => "doScrollTo();"
		], we_html_element::htmlForm([
			'name' => "we_form",
			'method' => "post"
			], we_class::hiddenTrans() .
			$content .
			we_html_element::htmlHidden("we_complete_request", 1)
		) .
		we_wysiwyg_editor::getHTMLConfigurationsTag()
	)
);

