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

	public function __construct($width = '', $ids = '', $cmd_del = '', $addbut = '', $ws = '', $fields = 'Icon,Path', $table = FILE_TABLE, $css = 'defaultfont', $thirdDelPar = '', $extraDelFn = ''){
		parent::__construct($width, $ids, $cmd_del, $addbut, $ws, $fields, $table, $css, $thirdDelPar, $extraDelFn);
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
	<td><img src="' . TREE_ICON_DIR . $this->Record[$this->fieldsArr[0]] . '" width="16" height="18" /></td>
	<td class="' . $this->css . '">' . $this->Record[$this->fieldsArr[1]] . '</td>
	<td>' . ((($this->isEditable() && $this->cmd_del) || $this->CanDelete) ?
						we_html_button::create_button("image:btn_function_trash", "javascript:if(typeof(_EditorFrame)!='undefined'){_EditorFrame.setEditorIsHot(true);}" . ($this->extraDelFn ? : "") . "; " . $_catFieldJS, true, 26) :
						"") . '</td>
</tr>';
		}
		return '';
	}

	function getRootLine($lineNr){
		switch($lineNr){
			case 0:
				return '<tr>
	<td><img src="' . TREE_ICON_DIR . we_base_ContentTypes::FOLDER_ICON . '" width="16" height="18" /></td>
	<td class="' . $this->css . '">/</td>
	<td>' . ((($this->isEditable() && $this->cmd_del) || $this->CanDelete) ?
						we_html_button::create_button("image:btn_function_trash", "javascript:if(typeof(_EditorFrame)!='undefined'){_EditorFrame.setEditorIsHot(true);}" . ($this->extraDelFn ? : "") . ";we_cmd('" . $this->cmd_del . "','0');", true, 26) :
						'') . '</td>
</tr>';
		}
		return '';
	}

	function getTableRows(){
		$out = '	<tr><td width="20">' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(50, 2) . '</td><td width="26">' . we_html_tools::getPixel(26, 2) . '</td></tr>';
		$this->nr = 0;
		$idArr = makeArrayFromCSV($this->ids);

		foreach($idArr as $id){
			$this->Record = getHash('SELECT ID,' . $this->fields . ' FROM ' . $this->db->escape($this->table) . ' WHERE ID =' . intval($id), $this->db);
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
		$out .= '	<tr><td width="20">' . we_html_tools::getPixel(20, count($idArr) ? 2 : 12) . '</td><td>' . we_html_tools::getPixel(50, 2) . '</td><td width="26">' . we_html_tools::getPixel(26, 2) . '</td></tr>';
		return $out;
	}

	function get(){
		$out = '<table border="0" cellpadding="0" height="18" cellspacing="0" width="' . abs($this->width - 10) . '" id="' . $this->rowPrefix . 'CatTable">' .
			$this->getTableRows() .
			'</table>';

		return '<table border="0" cellpadding="0" cellspacing="0" width="' . $this->width . '">
<tr><td><div style="background-color:white;" class="multichooser">' . $out . '</div></td></tr>
' . ($this->addbut ? ('<tr><td>' . we_html_tools::getPixel(2, 5) . '</td></tr>
<tr><td align="right">' . $this->addbut . '</td></tr>') : '') . '</table>' . "\n";
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
