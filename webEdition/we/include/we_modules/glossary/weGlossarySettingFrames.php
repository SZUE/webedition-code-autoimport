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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class weGlossarySettingFrames{
	private $Frameset;
	var $Controller;
	var $db;

	function __construct(){
		$this->Frameset = WE_MODULES_DIR . 'glossary/edit_glossary_settings_frameset.php';
		$this->Controller = new weGlossarySettingControl();
		$this->db = new DB_WE();
	}

	function getHTML($what){
		switch($what){
			case 'frameset':
				print $this->getHTMLFrameset();
				break;
			default:
				t_e(__FILE__ . " unknown reference: $what");
		}
	}

	function getHTMLFrameset(){
		return
			we_html_tools::htmlTop() .
			STYLESHEET .
			we_html_element::jsScript(JS_DIR . 'formFunctions.js') .
			'</head>' .
			we_html_element::htmlBody(array('class' => 'weDialogBody', 'style' => 'background-image: url(' . IMAGE_DIR . 'backgrounds/aquaBackground.gif);background-repeat:repeat;margin: 0px;position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;')
				, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
					, we_html_element::htmlExIFrame('content', $this->getHTMLContent(), 'position:absolute;top:0px;bottom:1px;left:0px;right:0px;overflow: hidden;') .
					we_html_element::htmlIFrame('cmdFrame', HTML_DIR . 'white.html', 'position:absolute;height:1px;bottom:0px;left:0px;right:0px;overflow: hidden;')
				)) . '</html>';
	}

	function getHTMLContent(){
		$configFile = WE_GLOSSARY_MODULE_PATH . weGlossaryReplace::configFile;
		if(!file_exists($configFile) || !is_file($configFile)){
			weGlossarySettingControl::saveSettings(true);
		}
		include($configFile);

		// Automatic Replacement
		$content = we_forms::checkboxWithHidden($GLOBALS['weGlossaryAutomaticReplacement'], 'GlossaryAutomaticReplacement', g_l('modules_glossary', '[enable_replacement]'));

		$parts = array(
			array(
				'headline' => "",
				'space' => 0,
				'html' => $content,
				'noline' => 1)
		);

		return
			'<form name="we_form" target="cmdFrame" action="' . $this->Frameset . '">
	' . we_html_tools::hidden('cmd', 'save_glossary_setting') . '
	' . we_multiIconBox::getHTML('GlossaryPreferences', "100%", $parts, 30, we_button::position_yes_no_cancel(
					we_button::create_button('save', 'javascript:document.we_form.submit();'), null, we_button::create_button('close', 'javascript:top.window.close();')), -1, '', '', false, g_l('modules_glossary', '[menu_settings]')) . '
	</form>';
	}

}
