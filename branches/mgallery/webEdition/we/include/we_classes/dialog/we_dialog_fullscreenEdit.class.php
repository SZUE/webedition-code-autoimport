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
		$e = new we_wysiwyg_editor("we_dialog_args[src]", $this->args["screenWidth"] - 90, $this->args["screenHeight"] - 200, '', $this->args["propString"], $this->args["bgcolor"], $this->args["editname"], $this->args["className"], $this->args["outsideWE"], $this->args["outsideWE"], $this->args["xml"], $this->args["removeFirstParagraph"], true, $this->args["baseHref"], $this->args["charset"], $this->args["cssClasses"], $this->args['language'], '', true, false, 'top', true, $this->args["contentCss"], $this->args["origName"], $this->args["tinyParams"], $this->args["contextmenu"], false, $this->args["templates"], $this->args["formats"], $this->args["galleryTmplIDs"], $this->args["fontsizes"]);
		return we_wysiwyg_editor::getHeaderHTML() . we_html_element::jsElement('isFullScreen = true;') . $e->getHTML();
	}

	public static function getTinyMceJS(){
		return parent::getTinyMceJS() .
			we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'plugins/wefullscreen/js/fullscreen_init.js');
	}

	function getJs(){
		return we_html_element::jsScript(JS_DIR . 'we_dialog_fullscreenEdit.js', 'self.focus();');
	}

	protected function getCancelBut(){
		return we_html_button::create_button(we_html_button::CANCEL, "javascript:top.opener.tinyMCECallRegisterDialog({},'unregisterDialog');top.close();");
	}

}
