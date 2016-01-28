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
class we_dialog_lang extends we_dialog_base{
	var $dialogWidth = 370;
	var $JsOnly = true;
	var $changeableArgs = array(
		"lang"
	);

	function __construct($noInternals = false){
		parent::__construct();
		$this->dialogTitle = g_l('wysiwyg', '[language_title]');
		$this->noInternals = $noInternals;
		$this->defaultInit();
	}

	function defaultInit(){
		$this->args = array("lang" => "");
	}

	public static function getTinyMceJS(){
		return parent::getTinyMceJS() .
			we_html_element::jsScript(TINYMCE_JS_DIR . 'plugins/welang/js/lang_init.js');
	}

	function getJs(){
		return we_dialog_base::getJs() .
			(defined('GLOSSARY_TABLE') && !$this->noInternals ?
				we_html_element::jsElement('
function weSaveToGlossaryFn() {
	if(typeof(isTinyMCE) != "undefined" && isTinyMCE === true){
		document.we_form.elements[\'weSaveToGlossary\'].value = 1;
	} else{
		eval("var editorObj = top.opener.weWysiwygObject_"+document.we_form.elements["we_dialog_args[editname]"].value);
		document.we_form.elements[\'weSaveToGlossary\'].value = 1;
		if(editorObj.getSelectedText().length > 0) {
			document.we_form.elements[\'text\'].value = editorObj.getSelectedText();
		} else {
			document.we_form.elements[\'text\'].value = editorObj.getNodeUnderInsertionPoint("SPAN",true,false).innerHTML;
		}
	}
	document.we_form.submit();
}') : '');
	}

	function getDialogContentHTML(){
		return '<table border="0" cellpadding="0" cellspacing="0">
<tr><td>' . $this->getLangField("lang", g_l('wysiwyg', '[language]'), 260) . '</td></tr>
</table>
' .
			(defined('GLOSSARY_TABLE') && permissionhandler::hasPerm("NEW_GLOSSARY") && !$this->noInternals ?
				we_html_tools::hidden("weSaveToGlossary", 0) .
				we_html_tools::hidden("language", we_base_request::_(we_base_request::STRING, 'language', $GLOBALS['weDefaultFrontendLanguage'])) .
				we_html_tools::hidden("text", "") : ''
			);
	}

	function getDialogButtons(){
		$trashbut = we_html_button::create_button("image:btn_function_trash", "javascript:document.we_form.elements['we_dialog_args[lang]'].value='';weDoOk();");

		$buttons = array(
			$trashbut
		);

		if(defined('GLOSSARY_TABLE') && permissionhandler::hasPerm("NEW_GLOSSARY") && !$this->noInternals){
			$glossarybut = we_html_button::create_button("to_glossary", "javascript:weSaveToGlossaryFn();", true, 100);
			$buttons[] = $glossarybut;
		}

		$buttons[] = parent::getDialogButtons();

		return we_html_button::create_button_table($buttons);
	}

}
