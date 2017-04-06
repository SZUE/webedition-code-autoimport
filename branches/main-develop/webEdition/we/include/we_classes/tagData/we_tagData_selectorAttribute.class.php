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
class we_tagData_selectorAttribute extends we_tagData_attribute{
	const FOLDER = 'folder';

	/**
	 * @var string
	 */
	var $Table;

	/**
	 * @var string
	 */
	var $Selectable;
	private $useName;

	/**
	 * @param string $name
	 * @param string $table
	 * @param string $selectable
	 * @param boolean $required
	 */
	function __construct($name, $table, $selectable, $required = false, $module = '', $useName = false, $description = '', $deprecated = false){
		$this->Table = $table;
		$this->Selectable = $selectable;
		$this->useName = $useName;
		parent::__construct($name, $required, $module, $description, $deprecated);
	}

	/**
	 * @return string
	 */
	function getCodeForTagWizard(){

		switch($this->Table){
			case CATEGORY_TABLE:
				$weCmd = 'we_selector_category';
				$this->Selectable = '';
				break;
			case NAVIGATION_TABLE:
				$weCmd = 'we_selector_file';
				break;
			default:
				$weCmd = ($this->Selectable == self::FOLDER ? 'we_selector_directory' : 'we_selector_document');
		}

		$input = we_html_element::htmlInput([
			'name' => $this->Name,
				'value' => $this->Value,
				'id' => $this->getIdName(),
				'class' => 'wetextinput'
				]);
		$button = we_html_button::create_button('select', "javascript:we_cmd('" . $weCmd . "', document.getElementById('" . $this->getIdName() . "').value, '" . $this->Table . "','','" . ($this->useName ? $this->getIdName() : '') . "', '', '', '', '" . $this->Selectable . "')");

		return '<table class="attribute"><tr>
						<td class="attributeName">' . $this->getLabelCodeForTagWizard() . '</td>
						<td class="attributeField">' . $input . '</td>
						<td class="attributeButton">' . $button . '</td>
					</tr></table>';
	}

}

//FIXME: remove
class weTagData_selectorAttribute extends we_tagData_selectorAttribute{

}