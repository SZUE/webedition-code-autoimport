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
class we_chooser_multiDir{

	protected $width = 388;
	protected $table = FILE_TABLE;
	protected $db;
	var $ids = '';
	var $ws = '';
	var $wsArr = [];
	var $cmd_del = '';
	var $addbut = '';
	var $css = '';
	var $nr = 0;
	protected $lines = 1;
	var $CanDelete = false;
	var $isEditable = true;
	var $extraDelFn = '';
	var $thirdDelPar = '';
	protected $Record = [];
	protected $onchangeSetHot = true;
	protected $fields = ['ID', 'Path'];

	public function __construct($width, $ids, $cmd_del, $addbut, $ws = "", $ct = 'ContentType', $table = FILE_TABLE, $css = "defaultfont", $thirdDelPar = "", $extraDelFn = ""){
		$this->db = new DB_WE();
		$this->width = $width;
		$this->ids = $ids;
		$this->table = $table;
		$this->cmd_del = $cmd_del;
		$this->addbut = $addbut;
		$this->css = $css;
		$this->extraDelFn = $extraDelFn;
		$this->thirdDelPar = $thirdDelPar;
		switch($table){
			case defined('CUSTOMER_TABLE') ? CUSTOMER_TABLE : 'CUSTOMER_TABLE':
				$this->fields = ['ID', 'Username'];
		}
		$this->fields[] = $ct;
	}

	function getLine($lineNr){
		switch($lineNr){
			case 0:
				return '<tr>
	<td class="chooserFileIcon" data-contenttype="' . $this->Record['ContentType'] . '"></td>
	<td class="' . $this->css . '">' . $this->Record['Path'] . '</td>
	<td class="buttons">' . ((($this->isEditable && $this->cmd_del) || $this->CanDelete) ?
						we_html_button::create_button(we_html_button::TRASH, "javascript:" . $this->getJsSetHot() . ($this->extraDelFn ?: "") . ";we_cmd('" . $this->cmd_del . "','" . $this->Record["ID"] . "'" . (strlen($this->thirdDelPar) ? ",'" . $this->thirdDelPar . "'" : "") . ");") :
						'') . '</td>
</tr>';
		}
	}

	function getRootLine($lineNr){
		switch($lineNr){
			case 0:
				return '<tr>
	<td class="chooserFileIcon" data-contenttype="' . we_base_ContentTypes::FOLDER . '"></td>
	<td class="' . $this->css . '">/</td>
	<td class="buttons">' . ((($this->isEditable && $this->cmd_del) || $this->CanDelete) ?
						we_html_button::create_button(we_html_button::TRASH, "javascript:" . $this->getJsSetHot() . ($this->extraDelFn ?: "") . ";we_cmd('" . $this->cmd_del . "','0');") :
						'') . '</td>
</tr>';
		}
	}

	function setOnchangeSetHot($setHot = true){
		$this->onchangeSetHot = $setHot;
	}

	function getJsSetHot(){
		return $this->onchangeSetHot ? "we_cmd('setHot');" : '';
	}

	function get(we_base_jsCmd $jsCmd){
		$out = '<table class="default" style="width: 100%;">';

		$this->nr = 0;
		$idArr = is_array($this->ids) ? $this->ids : ($this->ids === '' ? [] : explode(',', trim($this->ids, ',')));

		foreach($idArr as $id){
			$this->Record = getHash('SELECT ' . $this->fields[0] . ' AS ID,' . $this->fields[1] . ' AS Path,' . $this->fields[2] . ' AS ContentType FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($id), $this->db);
			if($this->Record){
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
		$out .= '</table>';

		$jsCmd->addCmd('setIconOfDocClass', 'chooserFileIcon');

		return '<table class="default">
<tr><td><div class="multichooser"' . ($this->width ? ' style="width:' . $this->width . 'px"' : '') . '>' . $out . '</div></td></tr>
' . ($this->addbut ? '<tr><td style="text-align:right;padding-top:5px;">' . $this->addbut . '</td></tr>' : '') . '</table>';
	}

}
