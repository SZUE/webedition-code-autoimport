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
		$catFieldJS = '';
		/* 	if($this->catField){
		  $ids = str_replace("," . $this->Record["ID"] . ",", ",", $this->ids);
		  } */
		$catFieldJS .= "deleteCategory('" . $this->rowPrefix . "'," . $this->Record["ID"] . "); ";
		switch($lineNr){
			case 0:
				return '<tr id="' . $this->rowPrefix . 'Cat' . $this->Record["ID"] . '">
	<td class="chooserFileIcon" data-contenttype="' . $this->Record['ContentType'] . '"></td>
	<td class="' . $this->css . '">' . $this->Record['Path'] . '</td>
	<td class="buttons">' . ((($this->isEditable && $this->cmd_del) || $this->CanDelete) ?
					we_html_button::create_button(we_html_button::TRASH, "javascript:" . $this->getJsSetHot() . ($this->extraDelFn ?: "") . "; " . $catFieldJS) :
					"") . '</td>
</tr>';
		}
		return '';
	}

	function getRootLine($lineNr){
		switch($lineNr){
			case 0:
				return '<tr>
	<td class="chooserFileIcon" data-contenttype="' . we_base_ContentTypes::FOLDER . '"></td>
	<td class="' . $this->css . '">/</td>
	<td class="buttons">' . ((($this->isEditable() && $this->cmd_del) || $this->CanDelete) ?
					we_html_button::create_button(we_html_button::TRASH, "javascript:" . $this->getJsSetHot() . ($this->extraDelFn ?: "") . ";we_cmd('" . $this->cmd_del . "','0');") :
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
			$this->Record = getHash('SELECT ' . $this->fields[0] . ' AS ID,' . $this->fields[1] . ' AS Path,' . $this->fields[2] . ' AS ContentType FROM ' . $this->db->escape($this->table) . ' WHERE ID =' . intval($id), $this->db);
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

	function get(we_base_jsCmd $jsCmd){
		$out = '<table class="default" style="height:18px;width: 100%;" id="' . $this->rowPrefix . 'CatTable">' .
			$this->getTableRows() .
			'</table>';
		$jsCmd->addCmd('setIconOfDocClass', 'chooserFileIcon');

		return '<table class="default">
<tr><td><div style="background-color:white;" class="multichooser"'.($this->width?' style="width:' . $this->width . 'px"':'').'>' . $out . '</div></td></tr>
' . ($this->addbut ? ('<tr><td style="text-align:right;padding-top:2px;">' . $this->addbut . '</td></tr>') : '') . '</table>';
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
