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

	protected function i_saveContentDataInDB(){
		if(($code = $this->getElement('data'))){
			$metas = $this->getMetas($code);
			if(!empty($metas['title'])){
				$this->setElement('Title', $metas['title']);
			}
			if(!empty($metas['description'])){
				$this->setElement('Description', $metas['description']);
			}
			if(!empty($metas['keywords'])){
				$this->setElement('Keywords', $metas['keywords']);
			}
			if(!empty($metas['charset'])){
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
			$code = preg_replace('/<meta http-equiv="Content-Type" content=".*>|<meta charset=".*>/i', we_html_tools::htmlMetaCtCharset($cs), $code);
		}
		return $code;
	}

	public function getPropertyPage(we_base_jsCmd $jsCmd){
		return we_html_multiIconBox::getHTML('PropertyPage', [
				['icon' => we_html_multiIconBox::PROP_PATH, 'headline' => g_l('weClass', '[path]'), 'html' => $this->formPath(),
					'space' => we_html_multiIconBox::SPACE_ICON],
				['icon' => we_html_multiIconBox::PROP_DOC, 'headline' => g_l('weClass', '[document]'), 'html' => $this->formDocTypeTempl(), 'space' => we_html_multiIconBox::SPACE_ICON],
				['icon' => we_html_multiIconBox::PROP_CATEGORIES, 'headline' => g_l('global', '[categorys]'), 'html' => $this->formCategory($jsCmd), 'space' => we_html_multiIconBox::SPACE_ICON],
				['icon' => we_html_multiIconBox::PROP_NAVI, 'headline' => g_l('global', '[navigation]'), 'html' => $this->formNavigation($jsCmd), 'space' => we_html_multiIconBox::SPACE_ICON],
				['icon' => we_html_multiIconBox::PROP_COPY, 'headline' => g_l('weClass', '[copy' . $this->ContentType . ']'), 'html' => $this->formCopyDocument(), 'space' => we_html_multiIconBox::SPACE_ICON],
				['icon' => we_html_multiIconBox::PROP_CHARSET, 'headline' => g_l('weClass', '[Charset]'), 'html' => $this->formCharset(), 'space' => we_html_multiIconBox::SPACE_ICON],
				['icon' => we_html_multiIconBox::PROP_USER, 'headline' => g_l('weClass', '[owners]'), 'html' => $this->formCreatorOwners($jsCmd), 'space' => we_html_multiIconBox::SPACE_ICON]]
		);
	}

}
