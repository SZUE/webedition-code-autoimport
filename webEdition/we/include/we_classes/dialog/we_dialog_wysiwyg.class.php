<?php

/**
 * webEdition CMS
 *
 * $Rev: 13353 $
 * $Author: mokraemer $
 * $Date: 2017-02-13 17:50:25 +0100 (Mo, 13 Feb 2017) $
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
class we_dialog_wysiwyg extends we_dialog_base{
	protected $writeToFrontend = false;
	protected $name = '';
	protected $fieldName = '';
	protected $readyConfig = [];
	protected $editorType = '';

	public static function getDialog($nointernals = false, $editorType = we_wysiwyg_editor::TYPE_INLINE_FALSE){
		$inst = new we_dialog_wysiwyg($nointernals, $editorType);
		$inst->initByHttp($editorType);

		return $inst->getHTML();
	}

	public function initByHttp($editorType = we_wysiwyg_editor::TYPE_INLINE_FALSE){
		parent::initByHttp();

		$this->editorType = $editorType;
		$this->readyConfig = (array) json_decode($this->args['readyConfig']);

		$this->charset = $this->readyConfig['weCharset']; // TODO: make these normal props
		if(!$this->charset){
			t_e('charset not found for wysiwyg', $this->charset);
			exit();
		}

		$this->name = $this->readyConfig['weName'];
		$this->fieldName = $this->readyConfig['weFieldName'];
		
		if($this->editorType === we_wysiwyg_editor::TYPE_FULLSCREEN){
			$this->JsOnly = true; // means: on clicking ok do js only (not submit form to cmd_frame)
			$this->pageNr = $this->numPages = 1;
			$this->dialogTitle = g_l('wysiwyg', '[fullscreen_editor]');
		} else {
			
		}


		// depending on type = inlineFalse or fullsccreen we must set: some args (width, height, isfullscreen) and props to manage ok-action
		// => we_what, we_cmd[0], $jsOnly and $page

		/*
		$this->what = we_base_request::_(we_base_request::STRING, 'we_what', '');
		// we need all fields to initialize we_wysiwyg_editor
		*/
	}

	protected function getJs(){
		return parent::getJs() .
			we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'plugins/wefullscreen/js/fullscreen_init.js') .
			we_html_element::jsScript(JS_DIR . 'dialogs/we_dialog_wysiwyg.js', 'self.focus();');
	}

	function getDialogContentHTML(){
		$e = new we_wysiwyg_editor($this->readyConfig, $this->editorType);

		// FIXME: add div around editor when fullscreen
		return we_html_element::htmlDiv(['style' => 'position:absolute;top:0;bottom:0px;left:0px;right:0px;overflow:hidden;margin:0px'],
				we_wysiwyg_editor::getHeaderHTML(false, $this->noInternals) .
				we_html_element::htmlHiddens([
						//'we_dialog_args[readyConfig]' => '', // Fno need to send this => FIXME: is overwritten by base
						//'we_cmd[0]' => $this->editorType === we_wysiwyg_editor::TYPE_INLINE_FALSE ? 'writeBack_inlineFalse' : ''
					]) . $e->getHTML()
			);
	}

	protected function getCancelBut(){
		return ($this->editorType !== we_wysiwyg_editor::TYPE_FULLSCREEN) ? parent::getCancelBut() :
			we_html_button::create_button(we_html_button::CANCEL, "javascript:top.opener.tinyMCECallRegisterDialog({},'unregisterDialog');top.close();");
	}

	function cmdFunction(){
		switch($this->we_cmd[0]){
			case 'writeBack_inlineFalse':
			case 'open_wysiwyg_window':
				// do we need this twice?
				if(!$this->writeToFrontend){
					if(preg_match('%^(.+_te?xt)\[.+\]$%i', $this->name)){
						$reqName = preg_replace('/^(.+_te?xt)\[.+\]$/', '${1}', $this->name);
					} else if(preg_match('|^(.+_input)\[.+\]$|i', $this->name)){
						$reqName = preg_replace('/^(.+_input)\[.+\]$/', '${1}', $this->name);
					}
				} else {
					$reqName = str_replace('[' . $this->fieldName . ']', '', $this->name);
				}

				$value = preg_replace('|(</?)script([^>]*>)|i', '${1}scr"+"ipt${2}', strtr(we_base_request::_(we_base_request::RAW_CHECKED, $reqName, '', $this->fieldName), [
					//"\r" => '\r',
					//"\n" => '\n',
					"'" => '&#039;'
				]));
				$replacements = [
					//'"' => '\"',
					"\xe2\x80\xa8" => '',
					"\xe2\x80\xa9" => '',
				];
				$taValue = strtr($value, $replacements);
				$divValue = $this->writeToFrontend ? $taValue : strtr(we_document::parseInternalLinks($value, 0), $replacements);

				return we_html_tools::getHtmlTop($this->fieldName, $this->charset, '', 
						we_html_element::jsScript(JS_DIR . 'dialogs/we_dialog_cmdFrame.js', "we_cmd('wysiwyg_writeBack')", [
								'id' => 'loadVarDialog_cmdFrame',
								'data-payload' => setDynamicVar(['textareaValue' => $taValue,
									'divValue' => $divValue,
									'name' => $this->name,
									'isFrontendEdit' => $this->writeToFrontend ? 1 : 0])
							]), we_html_element::htmlBody()
						);
		}
	}

}
