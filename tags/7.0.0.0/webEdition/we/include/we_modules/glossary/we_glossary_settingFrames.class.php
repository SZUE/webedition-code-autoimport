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
class we_glossary_settingFrames{

	private $Frameset;
	var $Controller;
	var $db;

	function __construct(){
		$this->Frameset = WE_MODULES_DIR . 'glossary/edit_glossary_settings_frameset.php';
		$this->Controller = new we_glossary_settingControl();
		$this->db = new DB_WE();
	}

	function getHTML($what){
		switch($what){
			case 'frameset':
				echo $this->getHTMLFrameset();
				break;
			default:
				t_e(__FILE__ . " unknown reference: $what");
		}
	}

	function getHTMLFrameset(){
		return
				we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET .
						we_html_element::jsScript(JS_DIR . 'formFunctions.js'), we_html_element::htmlBody(array('class' => 'weDialogBody')
								, we_html_element::htmlExIFrame('content', $this->getHTMLContent(), 'position:absolute;top:0px;bottom:1px;left:0px;right:0px;overflow: hidden;', '', '', false) .
								we_html_element::htmlIFrame('cmdFrame', 'about:blank', 'position:absolute;height:1px;bottom:0px;left:0px;right:0px;overflow: hidden;')
		));
	}

	function getHTMLContent(){
		// Automatic Replacement
		$content = we_html_forms::checkboxWithHidden(we_glossary_replace::useAutomatic(), 'GlossaryAutomaticReplacement', g_l('modules_glossary', '[enable_replacement]'));

		$parts = array(
			array(
				'headline' => "",
				'html' => $content,
				'noline' => 1)
		);

		return
				'<form name="we_form" target="cmdFrame" action="' . $this->Frameset . '">
	' . we_html_tools::hidden('cmd', 'save_glossary_setting') . '
	' . we_html_multiIconBox::getHTML('GlossaryPreferences', $parts, 30, we_html_button::position_yes_no_cancel(
								we_html_button::create_button(we_html_button::SAVE, 'javascript:document.we_form.submit();'), null, we_html_button::create_button(we_html_button::CLOSE, 'javascript:top.window.close();')), -1, '', '', false, g_l('modules_glossary', '[menu_settings]')) . '
	</form>';
	}

}
