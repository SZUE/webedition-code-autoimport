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
class weTagData_selectAttribute extends weTagDataAttribute{

	/**
	 * @var array
	 */
	var $Options;

	/**
	 * @param string $name
	 * @param array $options
	 * @param boolean $required
	 */
	function __construct($name, $options = array(), $required = false, $module = '', $description = '', $deprecated = false){
		if(!is_array($options)){
			return;
		}
		parent::__construct($name, $required, $module, $description, $deprecated);
		$this->Options = parent::getUseOptions($options);
	}

	static function getTrueFalse(){
		static $tmp = false;
		if(!$tmp){
			$tmp = array(new weTagDataOption('true'), new weTagDataOption('false'));
		}
		return $tmp;
	}

	/**
	 * @return string
	 */
	function getCodeForTagWizard(){
		$select = new we_html_select(array('name' => $this->getName(), 'id' => $this->getIdName(), 'class' => 'defaultfont selectinput'));
		if(!$this->Required){
			$select->addOption('', '');
		}

		foreach($this->Options as $option){
			$select->addOption($option->Value, $option->getName(), $option->Disabled ? array('disabled' => 'disabled') : array());
		}
		$select->selectOption($this->Value);

		return '<table class="attribute"><tr>
						<td class="attributeName">' . $this->getLabelCodeForTagWizard() . '</td>
						<td class="attributeField">' . $select->getHtml() . '</td>
					</tr></table>';
	}

}
