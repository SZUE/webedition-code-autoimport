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
/* the parent class of storagable webEdition classes */


class we_modules_view implements we_modules_viewIF{//FIXME is this really a base class, or is it an interface???
	var $db;
	var $frameset;
	var $topFrame;

	public function __construct($frameset = '', $topframe = 'top.content'){
		$this->db = new DB_WE();
		$this->setFramesetName($frameset);
		$this->setTopFrame($topframe);
	}

	//----------- Utility functions ------------------

	function htmlHidden($name, $value = ''){
		return we_html_element::htmlHidden(array('name' => trim($name), 'value' => oldHtmlspecialchars($value)));
	}

	//-----------------Init -------------------------------

	function setFramesetName($frameset){
		$this->frameset = $frameset;
	}

	function setTopFrame($frame){
		$this->topFrame = $frame;
	}

	//------------------------------------------------

	function getCommonHiddens($cmds = array()){
		return $this->htmlHidden('cmd', (isset($cmds['cmd']) ? $cmds['cmd'] : '')) .
			$this->htmlHidden('cmdid', (isset($cmds['cmdid']) ? $cmds['cmdid'] : '')) .
			$this->htmlHidden('pnt', (isset($cmds['pnt']) ? $cmds['pnt'] : '')) .
			$this->htmlHidden('tabnr', (isset($cmds['tabnr']) ? $cmds['tabnr'] : ''));
	}

	function getJSTop_tmp(){//taken from old edit_shop_frameset.php
		return we_html_element::jsScript(JS_DIR . 'images.js') .
			we_html_element::jsScript(JS_DIR . 'windows.js');
	}

	function getJSTop(){//TODO: is this shop-code or a copy paste from another module?
		return we_html_element::jsScript(JS_DIR . 'windows.js');
	}

	function getJSProperty(){
		return we_html_element::jsScript(JS_DIR . "windows.js");
	}

	function getJSTreeHeader(){

	}

	function getJSSubmitFunction($def_target = "edbody", $def_method = "post"){
		return '
function submitForm() {
	var f = arguments[3] ? self.document.forms[arguments[3]] : self.document.we_form;
	f.target = arguments[0]?arguments[0]:"' . $def_target . '";
	f.action = arguments[1]?arguments[1]:"' . $this->frameset . '";
	f.method = arguments[2]?arguments[2]:"' . $def_method . '";

	f.submit();
}';
	}

	public function processCommands(){
		switch(we_base_request::_(we_base_request::STRING, 'cmd', '')){
			case 'switchPage':
				break;
			default:
		}
	}

	public function processVariables(){
		$this->page = we_base_request::_(we_base_request::INT, 'page', $this->page);
	}

}
