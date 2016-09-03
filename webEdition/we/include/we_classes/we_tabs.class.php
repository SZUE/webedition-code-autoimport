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
class we_tabs{
	private $container = '';

	public function addTab($text, $isActive = false, $jscmd = '', $attribs = []){
		$class = ($isActive ? 'tabActive' : 'tabNormal');
		$att = '';
		if(isset($attribs) && is_array($attribs)){
			foreach($attribs as $key => $val){
				$att .= $key . '="' . $val . '" ';
			}
		}

		$this->container .= '<div ' . $att . ' onclick="if(weTabs.allowed_change_edit_page()){weTabs.setTabClass(this); ' . $jscmd . '}else{top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);}" class="' . $class . '"><span class="text">' . $text . '</span></div>';
	}

	static function getHeader($js = ''){
		return we_html_element::cssLink(CSS_DIR . 'we_tab.css') .
			we_html_element::jsElement('var weTabs=new (WE().layout.we_tabs)(document,window);' . $js);
	}

	function getHTML(){
		return '<div id="tabContainer" name="tabContainer">' . $this->container . '</div>';
	}

}
