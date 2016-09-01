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
class weTagData_typeAttribute extends weTagDataAttribute{

	/**
	 * @var array
	 */
	private $Options;

	/**
	 * @param string $name
	 * @param array $options
	 * @param boolean $required
	 */
	function __construct($name, $options = [], $required = true, $module = '', $description = '', $deprecated = false){
		if(!is_array($options)){
			return;
		}

		parent::__construct($name, $required, $module, $description, $deprecated);
		$this->Options = parent::getUseOptions($options);
		foreach($this->Options as &$option){
			$option->addTypeAttribute($this);
		}
		// overwrite value if needed
		if($this->Value === false){
			$this->Value = '-';
		}
	}

	/**
	 * @return string
	 */
	function getCodeForTagWizard(){
		$entries = ['' => g_l('taged', '[select_type]')];

		foreach($this->Options as $option){
			$entries[$option->Value] = ($option->getName() === '-' ? '' : $option->getName());
		}

		$js = "we_cmd('switch_type', this.value);";

		$select = new we_html_select(array(
			'name' => $this->Name,
			'id' => $this->getIdName(),
			'onchange' => $js,
			'class' => 'defaultfont selectinput'
		));
		$select->addOptions($entries);

		return '
<table class="attribute">
<tr>
	<td class="attributeName">' . $this->getLabelCodeForTagWizard() . '</td>
	<td class="attributeField">' . $select->getHtml() . '</td>
</tr>
</table>';
	}

	/**
	 * @return array
	 */
	function getOptions(){
		return $this->Options;
	}

}
