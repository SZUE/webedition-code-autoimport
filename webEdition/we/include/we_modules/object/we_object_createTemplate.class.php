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
class we_object_createTemplate extends we_template{

	function formDirChooser($width = "", $rootDirID = 0, $table = TEMPLATES_TABLE, $Pathname = "ParentPath", $IDName = "ParentID", $cmd = ""){
		$table = $table? : $this->Table;
		$textname = 'we_' . $this->Name . '_' . $Pathname;
		$idname = 'we_' . $this->Name . '_' . $IDName;
		$path = $this->$Pathname;
		//$myid = $this->$IDName;
		$cmd = "document.we_form.elements['" . $idname . "'].value";
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory'," . $cmd . ",'" . $table . "','" . we_base_request::encCmd($cmd) . "','" . we_base_request::encCmd("document.we_form.elements['" . $textname . "'].value") . "','','')");
		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($textname, 30, $path, "", ' readonly', "text", $width, 0), g_l('weClass', '[dir]'), "left", "defaultfont", we_html_element::htmlHidden($idname, 0), $button);
	}

	protected function formExtension2(){
		return we_html_tools::htmlFormElementTable("<b class='defaultfont'>" . $this->Extension . "</b>", g_l('weClass', '[extension]'));
	}

}
