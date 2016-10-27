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
class we_chooser_multiDirAndTemplate extends we_chooser_multiDir{
	private $classWSTemplates;

	public function __construct($width, $ids, $cmd_del, $addbut, $classWSTemplates = ""){
		parent::__construct($width, $ids, $cmd_del, $addbut, '', "Path", FILE_TABLE, "defaultfont");
		$this->lines = 1;

		$this->classWSTemplates = $classWSTemplates;
	}

	function getRootLine($lineNr){
		$but = we_html_button::create_button(we_html_button::VIEW, "javascript:we_cmd('object_preview_objectFile','0','" . (isset($this->classWSTemplates[0]) ? $this->classWSTemplates[0] : "") . "','" . $GLOBALS["we_transaction"] . "')");

		switch($lineNr){
			case 0:
				return '<tr>
	<td class="chooserFileIcon" data-contenttype="' . we_base_ContentTypes::FOLDER . '"></td>
	<td class="' . $this->css . '">/</td>
	<td class="buttonsObjectFile">' . $but . ((($this->isEditable && $this->cmd_del) || $this->CanDelete) ?
					we_html_button::create_button(we_html_button::TRASH, "javascript:" . $this->getJsSetHot() . ($this->extraDelFn ?: "") . ";we_cmd('" . $this->cmd_del . "','0');") :
					"") . '</td>
</tr>';
		}
	}

	function getLine($lineNr){
		$wsId = $this->Record["ID"];
		switch($lineNr){
			case 0:
				$but = we_html_button::create_button(we_html_button::VIEW, "javascript:we_cmd('object_preview_objectFile','0','" . (isset($this->classWSTemplates[$wsId]) ? $this->classWSTemplates[$wsId] : "") . "','" . $GLOBALS["we_transaction"] . "')");

				return '<tr>
	<td class="chooserFileIcon" data-contenttype="' . $this->Record['ContentType'] . '"></td>
	<td class="' . $this->css . '">' . $this->Record['Path'] . '</td>
	<td class="buttonsObjectFile">' . $but . ((($this->isEditable && $this->cmd_del) || $this->CanDelete) ?
					we_html_button::create_button(we_html_button::TRASH, "javascript:" . $this->getJsSetHot() . ($this->extraDelFn ?: "") . ";we_cmd('" . $this->cmd_del . "','" . $wsId . "'" . (strlen($this->thirdDelPar) ? ",'" . $this->thirdDelPar . "'" : "") . ");") :
					'') . '</td>
</tr>';
			case 1:
		}
	}

}
