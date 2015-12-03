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
class we_chooser_multiDirExtended extends we_chooser_multiDir{
	var $rowPrefix = '';
	var $catField = '';

	public function __construct($width = '', $ids = '', $cmd_del = '', $addbut = '', $ws = '', $ct = 'ContentType', $table = FILE_TABLE, $css = 'defaultfont', $thirdDelPar = '', $extraDelFn = ''){
		parent::__construct($width, $ids, $cmd_del, $addbut, $ws, $ct, $table, $css, $thirdDelPar, $extraDelFn);
	}

	function getLine($lineNr){
		$_catFieldJS = '';
		/* 	if($this->catField){
		  $_ids = str_replace("," . $this->Record["ID"] . ",", ",", $this->ids);
		  } */
		$_catFieldJS .= "deleteCategory('" . $this->rowPrefix . "'," . $this->Record["ID"] . "); ";
		switch($lineNr){
			case 0:
				return '<tr id="' . $this->rowPrefix . 'Cat' . $this->Record["ID"] . '">
	<td class="chooserFileIcon" data-contenttype="' . $this->Record['ContentType'] . '"></td>
	<td class="' . $this->css . '">' . $this->Record['Path'] . '</td>
	<td class="buttons">' . ((($this->isEditable() && $this->cmd_del) || $this->CanDelete) ?
						we_html_button::create_button(we_html_button::TRASH, "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);" . ($this->extraDelFn ? : "") . "; " . $_catFieldJS, true, 26) :
						"") . '</td>
</tr>';
		}
		return '';
	}

	function getRootLine($lineNr){
		switch($lineNr){
			case 0:
				return '<tr>
	<td class="chooserFileIcon" data-contenttype="folder"></td>
	<td class="' . $this->css . '">/</td>
	<td class="buttons">' . ((($this->isEditable() && $this->cmd_del) || $this->CanDelete) ?
						we_html_button::create_button(we_html_button::TRASH, "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);" . ($this->extraDelFn ? : "") . ";we_cmd('" . $this->cmd_del . "','0');", true, 26) :
						'') . '</td>
</tr>';
		}
		return '';
	}

	function getTableRows(){
		$out = '';
		$this->nr = 0;
		$idArr = makeArrayFromCSV($this->ids);

		foreach($idArr as $id){
			$this->Record = getHash('SELECT ID,Path,' . $this->ct . ' AS ContentType FROM ' . $this->db->escape($this->table) . ' WHERE ID =' . intval($id), $this->db);
			if(!empty($this->Record)){
				for($i = 0; $i < $this->lines; $i++){
					$out .= $this->getLine($i);
				}
			} else if(!$id){
				for($i = 0; $i < $this->lines; $i++){
					$out .= $this->getRootLine($i);
				}
			}
			$this->nr++;
		}
		return $out;
	}

	function get(){
		$out = '<table class="default" height="18" width="' . abs($this->width - 10) . '" id="' . $this->rowPrefix . 'CatTable">' .
			$this->getTableRows() .
			'</table>';

		return '<table class="default" width="' . $this->width . '">
<tr><td><div style="background-color:white;" class="multichooser">' . $out . '</div></td></tr>
' . ($this->addbut ? ('<tr><td style="text-align:right;padding-top:2px;">' . $this->addbut . '</td></tr>') : '') . '</table>' . we_html_element::jsElement('WE().util.setIconOfDocClass(document,"chooserFileIcon");');
	}

	function setRowPrefix($val){
		$this->rowPrefix = $val;
		return true;
	}

	function setCatField($val){
		$this->catField = $val;
		return true;
	}

}
