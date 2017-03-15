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
class we_editor_properties extends we_editor_base{

	public function show(){
		$weSuggest = & weSuggest::getInstance();
		$GLOBALS['we_transaction'] = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', we_base_request::_(we_base_request::TRANSACTION, 'we_transaction'), 2);

		$this->charset = ($this->we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES ?
				//	send charset, if one is set:
				$this->we_doc->getElement('Charset', 'dat', DEFAULT_CHARSET) :
				$GLOBALS['WE_BACKENDCHARSET']);

		we_html_tools::headerCtCharset('text/html', $this->charset);

		return $this->getPage($this->we_doc->getPropertyPage($this->jsCmd), '', ['onload' => "doScrollTo()"]);
	}

}
