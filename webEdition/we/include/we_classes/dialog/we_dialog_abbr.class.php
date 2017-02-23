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
class we_dialog_abbr extends we_dialog_base{

	public function __construct($noInternals = true){
		parent::__construct($noInternals);

		$this->onOkJsOnly = true;
		$this->changeableArgs = [
			'title',
			'lang',
			'class',
			'style'
		];
		$this->dialogTitle = g_l('wysiwyg', '[abbr_title]');

		$this->defaultInit();
		$this->initByHttp();
	}

	public static function getDialog($noInternals = true){
		$inst = new we_dialog_abbr($noInternals);

		return $inst->getHTML();
	}

	function defaultInit(){
		$this->args['title'] = '';
		$this->args['lang'] = '';
		$this->args['cssclass'] = '';
		$this->args['style'] = '';
	}

	function initByHttp(){
		if(defined('GLOSSARY_TABLE') && we_base_request::_(we_base_request::BOOL, 'weSaveToGlossary') && !$this->noInternals){
			$Glossary = new we_glossary_glossary();
			$Glossary->Language = we_base_request::_(we_base_request::STRING, 'language');
			$Glossary->Type = we_glossary_glossary::TYPE_ABBREVATION;
			$Glossary->Text = trim(we_base_request::_(we_base_request::STRING, 'text'));
			$Glossary->Title = trim(we_base_request::_(we_base_request::STRING, 'we_dialog_args', '', 'title'));
			$Glossary->Published = time();
			$Glossary->setAttribute('lang', we_base_request::_(we_base_request::STRING, 'we_dialog_args', '', 'lang'));
			$Glossary->setPath();

			if($Glossary->Title === ''){
				$this->jsCmd->addMsg(g_l('modules_glossary', '[title_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
				$this->jsCmd->addCmd('setFocus', "we_dialog_args[title]");
			} else if($Glossary->getAttribute('lang') === ''){
				$this->jsCmd->addMsg(g_l('modules_glossary', '[lang_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
				$this->jsCmd->addCmd('setFocus', "we_dialog_args[lang]");
			} else if($Glossary->Text === ''){
				$this->jsCmd->addMsg(g_l('modules_glossary', '[name_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
			} else if($Glossary->pathExists($Glossary->Path)){
				$this->jsCmd->addMsg(g_l('modules_glossary', '[name_exists]'), we_message_reporting::WE_MESSAGE_ERROR);
			} else {
				$Glossary->save();

				$Cache = new we_glossary_cache(we_base_request::_(we_base_request::STRING, 'language'));
				$Cache->write();
				unset($Cache);
				$this->jsCmd->addMsg(g_l('modules_glossary', '[entry_saved]'), we_message_reporting::WE_MESSAGE_NOTICE);
				$this->jsCmd->addCmd('close');
			}
		}

		parent::initByHttp();
	}

	protected function getJs(){
		return
			parent::getJs() .
			we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'plugins/weabbr/js/abbr_init.js');
	}

	function getDialogContentHTML(){
		$foo = we_html_tools::htmlTextInput("we_dialog_args[title]", 30, (isset($this->args["title"]) ? $this->args["title"] : ""), "", '', "text", 350);
		$title = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', '[title]'));

		$lang = $this->getLangField("lang", g_l('wysiwyg', '[language]'), 350);

		$table = '<table class="default">
<tr><td style="padding-bottom:10px;">' . $title . '</td></tr>
<tr><td>' . $lang . '</td></tr>
</table>';
		if(defined('GLOSSARY_TABLE') && we_base_permission::hasPerm("NEW_GLOSSARY")){
			$table .= we_html_element::htmlHiddens(['weSaveToGlossary' => 0,
					'language' => we_base_request::_(we_base_request::STRING, 'language', $GLOBALS['weDefaultFrontendLanguage']),
					'text' => ''
			]);
		}

		return $table;
	}

	function getDialogButtons(){
		$buttons = we_html_button::create_button(we_html_button::TRASH, "javascript:document.we_form.elements['we_dialog_args[title]'].value='';weDoOk();");

		if(defined('GLOSSARY_TABLE') && we_base_permission::hasPerm("NEW_GLOSSARY") && !$this->noInternals){
			$buttons .= we_html_button::create_button('to_glossary', "javascript:weSaveToGlossaryFn();");
		}

		$buttons .= parent::getDialogButtons();

		return $buttons;
	}

}
