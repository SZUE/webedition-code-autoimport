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

	public function __construct(){

	}

	public function addTab($tab){
		$this->container .= $tab->getHTML();
	}

	function getHeader(){
		return we_html_element::cssLink(CSS_DIR . 'we_tab.css') .
			we_html_element::jsScript(JS_DIR . 'we_tabs/we_tabs.js') .
			we_html_element::jsScript(JS_DIR . "attachKeyListener.js");
	}

	function getHTML(){
		return '<div id="tabContainer" name="tabContainer">' . $this->container . '</div>';
	}

}
