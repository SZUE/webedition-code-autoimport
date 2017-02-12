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
class we_dialog_acronym extends we_dialog_base{

	function __construct($noInternals = false){
		parent::__construct();
		$this->changeableArgs = ["title",
			"lang",
			"class",
			"style"
		];
		$this->JsOnly = true;
		$this->dialogTitle = g_l('wysiwyg', '[acronym_title]');
		$this->noInternals = $noInternals;
		$this->defaultInit();
	}

	function getOkJs(){
		return '
WeacronymDialog.insert();
top.close();
';
	}

	function defaultInit(){
		$this->args = [
			'title' => '',
			'lang' => '',
			'class' => '',
			'style' => ''
		];
	}

	protected function getJs(){
		return parent::getJs() .
			we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'plugins/weacronym/js/acronym_init.js');
	}

	function getDialogContentHTML(){
		$foo = we_html_tools::htmlTextInput("we_dialog_args[title]", 30, (isset($this->args["title"]) ? $this->args["title"] : ""), "", '', "text", 350);
		$title = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', '[title]'));

		$lang = $this->getLangField('lang', g_l('wysiwyg', '[language]'), 350);

		return '<table class="default">
<tr><td style="padding-bottom:10px;">' . $title . '</td></tr>
<tr><td>' . $lang . '</td></tr>
</table>
' .
			(defined('GLOSSARY_TABLE') && we_base_permission::hasPerm("NEW_GLOSSARY") && !$this->noInternals ?
			we_html_element::htmlHiddens(['weSaveToGlossary' => 0,
				'language' => we_base_request::_(we_base_request::STRING, 'language', $GLOBALS['weDefaultFrontendLanguage']),
				'text' => ''
			]) :
			'');
	}

	function getDialogButtons(){
		$buttons = we_html_button::create_button(we_html_button::TRASH, "javascript:document.we_form.elements['we_dialog_args[title]'].value='';weDoOk();");

		if(defined('GLOSSARY_TABLE') && we_base_permission::hasPerm('NEW_GLOSSARY') && !$this->noInternals){
			$buttons .= we_html_button::create_button('to_glossary', "javascript:weSaveToGlossaryFn();");
		}

		$buttons .= parent::getDialogButtons();

		return $buttons;
	}

}
