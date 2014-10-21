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
class we_htmlDocument extends we_textContentDocument{

	function __construct(){
		parent::__construct();
		$this->ContentType = we_base_ContentTypes::HTML;
	}

	function i_saveContentDataInDB(){
		if(($code = $this->getElement('data'))){
			$metas = $this->getMetas($code);
			if(isset($metas['title']) && $metas['title']){
				$this->setElement('Title', $metas['title']);
			}
			if(isset($metas['description']) && $metas['description']){
				$this->setElement('Description', $metas['description']);
			}
			if(isset($metas['keywords']) && $metas['keywords']){
				$this->setElement('Keywords', $metas['keywords']);
			}
			if(isset($metas['charset']) && $metas['charset']){
				$this->setElement('Charset', $metas['charset'], 'attrib');
			}
		}
		return parent::i_saveContentDataInDB();
	}

	function i_publInScheduleTable(){
		return (we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) ?
				we_schedpro::publInScheduleTable($this, $this->DB_WE) :
				false);
	}

	function getDocumentCode(){
		$code = $this->getElement('data');
		if(($cs = $this->getElement('Charset'))){
			$code = preg_replace('|<meta http-equiv="Content-Type" content=".*>|i', we_html_tools::htmlMetaCtCharset('text/html', $cs), $code);
		}
		return $code;
	}

}
