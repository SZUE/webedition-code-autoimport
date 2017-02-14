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

	public function __construct($noInternals = true){
		parent::__construct($noInternals);

		$this->changeableArgs = ["lang"
		];
		$this->JsOnly = true;
		$this->dialogTitle = g_l('wysiwyg', '[language_title]');
		$this->noInternals = $noInternals;
		$this->defaultInit();
		$this->initByHttp();
	}

	public static function getDialog($noInternals = true){
		$inst = new we_dialog_lang($noInternals);

		return $inst->getHTML();
	}

	function defaultInit(){
		$this->args = ["lang" => ""];
	}

	function initByHttp(){
		if(defined('GLOSSARY_TABLE') && we_base_request::_(we_base_request::BOOL, 'weSaveToGlossary') && !$this->noInternals){
			$Glossary = new we_glossary_glossary();
			$Glossary->Language = we_base_request::_(we_base_request::STRING, 'language', '');
			$Glossary->Type = we_glossary_glossary::TYPE_FOREIGNWORD;
			$Glossary->Text = trim(we_base_request::_(we_base_request::STRING, 'text'));
			$Glossary->Published = time();
			$Glossary->setAttribute('lang', we_base_request::_(we_base_request::STRING, 'we_dialog_args', '', 'lang'));
			$Glossary->setPath();

			if($Glossary->Text === "" || $Glossary->getAttribute('lang') === ""){
				$this->jsCmd->addMsg(g_l('modules_glossary', '[name_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
			} else if($Glossary->pathExists($Glossary->Path)){
				$this->jsCmd->addMsg(g_l('modules_glossary', '[name_exists]'), we_message_reporting::WE_MESSAGE_ERROR);
			} else {
				$Glossary->save();
				$this->jsCmd->addMsg(g_l('modules_glossary', '[entry_saved]'), we_message_reporting::WE_MESSAGE_NOTICE);
				$this->jsCmd->addCmd('close');
			}
		}

		parent::initByHttp();
	}

	protected function getJs(){
		return parent::getJs() .
			we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'plugins/welang/js/lang_init.js');
	}

	function getDialogContentHTML(){
		return '<table class="default">
<tr><td>' . $this->getLangField("lang", g_l('wysiwyg', '[language]'), 260) . '</td></tr>
</table>' .
			(defined('GLOSSARY_TABLE') && we_base_permission::hasPerm("NEW_GLOSSARY") && !$this->noInternals ?
			we_html_element::htmlHiddens(['weSaveToGlossary' => 0,
				'language' => we_base_request::_(we_base_request::STRING, 'language', $GLOBALS['weDefaultFrontendLanguage']),
				'text' => ''
			]) : ''
			);
	}

	function getDialogButtons(){
		$buttons = we_html_button::create_button(we_html_button::TRASH, "javascript:document.we_form.elements['we_dialog_args[lang]'].value='';weDoOk();");

		if(defined('GLOSSARY_TABLE') && we_base_permission::hasPerm("NEW_GLOSSARY") && !$this->noInternals){
			$buttons = we_html_button::create_button('to_glossary', "javascript:weSaveToGlossaryFn();");
		}

		$buttons .= parent::getDialogButtons();

		return $buttons;
	}

}
