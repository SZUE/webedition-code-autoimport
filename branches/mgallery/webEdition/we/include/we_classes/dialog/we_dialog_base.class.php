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
class we_dialog_base{
	/*	 * ***********************************************************************
	 * VARIABLES
	 * *********************************************************************** */
	var $db = '';
	var $what = '';
	var $args = array();
	var $dialogTitle = '';
	var $ClassName = __CLASS__;
	var $changeableArgs = array();
	var $pageNr = 1;
	var $numPages = 1;
	var $JsOnly = false;
	var $dialogWidth = 350;
	var $charset = '';
	var $tinyMCEPopupManagment = true;
	protected $noInternals = false;
	protected $we_cmd = array();

	/*	 * ***********************************************************************
	 * CONSTRUCTOR
	 * *********************************************************************** */

	/**
	 * Constructor of class.
	 *
	 * @return     we_dialog_base
	 */
	function __construct(){
		$this->db = new DB_WE();
	}

	/*	 * ***********************************************************************
	 * FUNCTIONS
	 * *********************************************************************** */

	function setTitle($title){
		$this->dialogTitle = $title;
	}

	function initByHttp(){
		$this->what = we_base_request::_(we_base_request::STRING, 'we_what', '');
		$this->we_cmd = we_base_request::_(we_base_request::RAW, 'we_cmd', array());

		if(($args = we_base_request::_(we_base_request::STRING, 'we_dialog_args'))){//assume no tags are allowed
			$this->args = $args;
			foreach($this->args as $key => $value){
				$this->args[$key] = urldecode($value);
			}
		}

		$this->pageNr = we_base_request::_(we_base_request::INT, 'we_pageNr', $this->pageNr);
	}

	function getHTML(){
		if($this->JsOnly){
			$this->what = 'dialog';
		}

		switch($this->what){
			case 'cmd':
				return $this->getCmdHTML();
			default:
				return $this->getHeaderHTML(true) .
					$this->getFramesetHTML() . '</html>';
		}
	}

	function getCmdHTML(){
		$send = array();

		// " quote for correct work within ""
		foreach($this->args as $k => $v){
			$send[$k] = str_replace('"', '\"', $v);
		}

		return $this->cmdFunction($send);
	}

	function cmdFunction(array $args){
		// overwrite
	}

	function getOkJs(){
		return '';
	}

	function getFramesetHTML(){
		return we_html_element::jsElement(
				((!(we_base_browserDetect::isGecko() || we_base_browserDetect::isOpera())) ? 'document.onkeydown = doKeyDown;' : '') . '

function doKeyDown() {
	var key = event.keyCode;

	switch(key) {
		case 27:
			top.close();
			break;

		case 13:
			self.we_' . $this->ClassName . '_edit_area.weDoOk();
			break;
	}
}') .
			we_html_element::htmlBody(array('class' => 'weDialogBody', 'style' => 'position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;', 'onunload' => 'doUnload()')
				, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
					, we_html_element::htmlExIFrame('main', $this->getDialogHTML(), 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;overflow: hidden;') .
					we_html_element::htmlIFrame('we_' . $this->ClassName . '_cmd_frame', 'about:blank', 'position:absolute;height:0px;bottom:0px;left:0px;right:0px;overflow: hidden;')
		));
	}

	protected function getNextBut(){
		return we_html_button::create_button(we_html_button::NEXT, "javascript:document.forms['0'].submit();");
	}

	protected function getOkBut(){
		return we_html_button::create_button(we_html_button::OK, 'javascript:weDoOk();');
	}

	protected function getCancelBut(){
		return we_html_button::create_button(we_html_button::CANCEL, 'javascript:top.close();');
	}

	protected function getbackBut(){
		return ($this->pageNr > 1) ? we_html_button::create_button(we_html_button::BACK, 'javascript:history.back();') : '';
	}

	function getDialogHTML(){
		$dc = $this->getDialogContentHTML();

		$dialogContent = (is_array($dc) ?
				we_html_multiIconBox::getHTML('', $dc, 30, $this->getDialogButtons(), -1, '', '', false, $this->dialogTitle) :
				we_html_tools::htmlDialogLayout($dc, $this->dialogTitle, $this->getDialogButtons()));

		return $this->getFormHTML() . $dialogContent .
			we_html_element::htmlHidden("we_what", "cmd") . $this->getHiddenArgs() . '</form>';
	}

	function getDialogButtons(){
		if($this->pageNr == $this->numPages && $this->JsOnly == false){
			$back = $this->getBackBut();
			$ok = we_html_button::create_button(we_html_button::OK, 'form:we_form');
			$okBut = $back ? $back . $ok : $ok;
		} else if($this->pageNr < $this->numPages){
			$back = $this->getBackBut();
			$next = $this->getNextBut();
			$okBut = ($back && $next ) ? $back . $next : ($back ? : $next );
		} else {
			$back = $this->getBackBut();
			$ok = $this->getOkBut();
			$okBut = ($back && $ok ) ? $back . $ok : ($back ? : $ok );
		}
		return we_html_button::position_yes_no_cancel($okBut, '', $this->getCancelBut());
	}

	function getFormHTML(){
		$hiddens = "";
		if(($cmd = we_base_request::_(we_base_request::STRING, 'we_cmd'))){
			foreach($cmd as $k => $v){
				//TODO: why should we loop this commands through?
				$hiddens .= '<input type="hidden" name="we_cmd[' . $k . ']" value="' . rawurlencode($v) . '"/>';
			}
		}

		//create some empty we_cmds to be filled by JS if needed
		for($i = 0; $i < 4; $i++){
			$hiddens .= isset($_REQUEST['we_cmd'][$i]) ? '' : we_html_element::htmlHidden("we_cmd[$i]", '');
		}

		$target = (!$this->JsOnly ? ' target="we_' . $this->ClassName . '_cmd_frame"' : '');

		return '<form name="we_form" action="' . $_SERVER["SCRIPT_NAME"] . '" method="post"' . $target . $this->getFormJsOnSubmit() . '>' . $hiddens;
	}

	function getFormJsOnSubmit(){
		return '';
	}

	function getHiddenArgs(){
		$hiddenArgs = '';

		foreach($this->args as $k => $v){
			if(!in_array($k, $this->changeableArgs)){
				$hiddenArgs .= we_html_element::htmlHidden('we_dialog_args[' . $k . ']', $v);
			}
		}
		return $hiddenArgs;
	}

	function getDialogContentHTML(){
		return ''; // overwrite !!
	}

	function getHeaderHTML($printJS_Style = false, $additionals = ''){
		return we_html_tools::getHtmlTop($this->dialogTitle, $this->charset) . ($printJS_Style ? STYLESHEET : '') . static::getTinyMceJS() .
			($printJS_Style ?
				we_html_element::jsScript(JS_DIR . 'windows.js') .
				we_html_element::jsScript(JS_DIR . 'global.js') .
				$this->getJs() :
				''
			) . we_html_element::cssLink(CSS_DIR . 'wysiwyg/tinymce/weDialogCss.css') . $additionals .
			'</head>';
	}

	public static function getTinyMceJS(){
		return
			we_html_element::jsElement('var isWeDialog = true;') .
			we_html_element::jsScript(TINYMCE_SRC_DIR . 'tiny_mce_popup.js') .
			we_html_element::jsScript(TINYMCE_SRC_DIR . 'utils/mctabs.js') .
			we_html_element::jsScript(TINYMCE_SRC_DIR . 'utils/form_utils.js') .
			we_html_element::jsScript(TINYMCE_SRC_DIR . 'utils/validate.js') .
			we_html_element::jsScript(TINYMCE_SRC_DIR . 'utils/editable_selects.js');
	}

	function getJs(){
		return we_html_element::jsElement('
var textareaFocus = false;
var onEnterKey=' . intval($this->pageNr == $this->numPages && $this->JsOnly) . ';

function weDoOk() {' .
				($this->pageNr == $this->numPages && $this->JsOnly ? '
	if (!textareaFocus) {
		' . $this->getOkJs() . '
	}' :
					'') . '
}') .
			we_html_element::jsScript(JS_DIR . 'dialogs/we_dialog_base.js', 'addKeyListener();self.focus();');
	}

	function getHttpVar($type, $name, $alt = ""){
		return we_base_request::_($type, "we_dialog_args", $alt, $name);
	}

	function getLangField($name, $title, $width){
		//FIXME: these values should be obtained from global settings
		$foo = we_html_tools::htmlTextInput("we_dialog_args[" . $name . "]", 15, (isset($this->args[$name]) ? $this->args[$name] : ""), "", '', "text", $width - 50);
		$foo2 = '<select style="width:50px;" class="defaultfont" name="' . $name . '_select" size="1" onchange="this.form.elements[\'we_dialog_args[' . $name . ']\'].value=this.options[this.selectedIndex].value;this.selectedIndex=-1;">
	<option value=""></option>
	<option value="en">en</option>
	<option value="de">de</option>
	<option value="es">es</option>
	<option value="fi">fi</option>
	<option value="ru">ru</option>
	<option value="fr">fr</option>
	<option value="nl">nl</option>
	<option value="pl">pl</option>
</select>';
		return we_html_tools::htmlFormElementTable($foo, $title, "left", "defaultfont", $foo2);
	}

	function getClassSelect($style = 'width: 300px;'){
		$clSelect = new we_html_select(array(
			"name" => "we_dialog_args[cssclass]",
			"id" => "we_dialog_args[cssclass]",
			"size" => 1,
			"style" => $style,
			'class' => 'defaultfont'
		));
		$clSelect->addOption("", g_l('wysiwyg', '[none]'));
		$classesCSV = trim($this->args['cssclasses'], ",");
		if($classesCSV){
			foreach(explode(",", $classesCSV) as $val){
				$clSelect->addOption($val, "." . $val);
			}
		}
		if(!empty($this->args["cssclass"])){
			$clSelect->selectOption($this->args["cssclass"]);
		}

		return $clSelect->getHTML() . we_html_element::htmlHidden("we_dialog_args[cssclasses]", $classesCSV);
	}

}
