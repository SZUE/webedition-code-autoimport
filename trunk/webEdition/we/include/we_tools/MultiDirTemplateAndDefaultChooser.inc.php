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
class MultiDirTemplateAndDefaultChooser extends MultiDirAndTemplateChooser{

	private $defaultName = '';
	private $defaultArr = array();

	public function __construct($width, $ids, $cmd_del, $addbut, $ws = "", $tmplcsv = "", $tmplSelectName = "", $mustTemplateIDs = "", $tmplWs = "", $defaultName = "", $defaultCSV = "", $fields = "Icon,Path", $table = FILE_TABLE, $css = "defaultfont"){
		$this->defaultName = $defaultName;
		$this->defaultArr = makeArrayFromCSV($defaultCSV);
		parent::__construct($width, $ids, $cmd_del, $addbut, $ws, $tmplcsv, $tmplSelectName, $mustTemplateIDs, $tmplWs, $fields, $table, $css);
		$this->lines = 3;
	}

	function getRootLine($lineNr){

		switch($lineNr){
			case 0:
				return MultiDirAndTemplateChooser::getRootLine($lineNr);
			default:
				return $this->getLine($lineNr);
		}
	}

	function getLine($lineNr){

		//$editable = $this->isEditable();
		switch($lineNr){
			case 0:
				return MultiDirAndTemplateChooser::getLine(0);
			case 1:
				$idArr = makeArrayFromCSV($this->ids);
				$checkbox = we_html_forms::checkbox($idArr[$this->nr], (in_array($idArr[$this->nr], $this->defaultArr) ? true : false), $this->defaultName . "_" . $this->nr, g_l('weClass', '[standard_workspace]'));
				return '<tr><td></td><td>' . $checkbox . '</td><td>' . we_html_tools::getPixel(50, 1) . '</td></tr>';
			case 2:
				return MultiDirAndTemplateChooser::getLine(1);
		}
	}

}
