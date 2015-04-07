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
class we_dialog_fullscreenEdit extends we_dialog_base{

	var $JsOnly = true;
	var $ClassName = __CLASS__;
	var $changeableArgs = array("src");

	function __construct(){
		parent::__construct();
		$this->dialogTitle = g_l('wysiwyg', '[fullscreen_editor]');
		$this->args["src"] = "";
	}

	function getDialogContentHTML(){
		$e = new we_wysiwyg_editor("we_dialog_args[src]", $this->args["screenWidth"] - 90, $this->args["screenHeight"] - 200, '', $this->args["propString"], $this->args["bgcolor"], $this->args["editname"], $this->args["className"], $this->args["outsideWE"], $this->args["outsideWE"], $this->args["xml"], $this->args["removeFirstParagraph"], true, $this->args["baseHref"], $this->args["charset"], $this->args["cssClasses"], $this->args['language'], '', true, false, 'top', true, $this->args["contentCss"], $this->args["origName"], $this->args["tinyParams"], $this->args["contextmenu"], false, $this->args["templates"], $this->args["formats"]);
		return we_wysiwyg_editor::getHeaderHTML() . we_html_element::jsElement('isFullScreen = true;') . $e->getHTML();
	}

	public static function getTinyMceJS(){
		return parent::getTinyMceJS() .
			we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'plugins/wefullscreen/js/fullscreen_init.js');
	}

	function getJs(){
		return we_html_element::jsScript(JS_DIR . 'windows.js') . we_html_element::jsElement('
var textareaFocus = false;

' . (we_base_browserDetect::isGecko() || we_base_browserDetect::isOpera() ? 'document.addEventListener("keyup",doKeyDown,true);' : 'document.onkeydown = doKeyDown;') . '

function doKeyDown(e) {
	var key = (e.charCode === undefined ?event.keyCode:e.charCode);
	switch (key) {
		case 27:
			top.close();
			break;
	}
}

function weDoOk() {
	top.opener.tinyMCECallRegisterDialog({},"unregisterDialog");
	WefullscreenDialog.writeback();
	top.close();
}

function IsDigit(e) {
	var key = (e.charCode === undefined ?event.keyCode:e.charCode);
	return (((key >= 48) && (key <= 57)) || (key == 0) || (key == 13));
}

function IsDigitPercent(e) {
	var key = (e.charCode === undefined ?event.keyCode:e.charCode);
	return (((key >= 48) && (key <= 57)) || (key == 37) || (key == 0)  || (key == 13));
}

function doUnload() {
	if (jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

self.focus();');
	}

	protected function getCancelBut(){
		return we_html_button::create_button("cancel", "javascript:top.opener.tinyMCECallRegisterDialog({},'unregisterDialog');top.close();");
	}

}
