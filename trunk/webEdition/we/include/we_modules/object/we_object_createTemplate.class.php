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
class we_object_createTemplate extends we_template {

	function formDirChooser($width = "", $rootDirID = 0, $table = TEMPLATES_TABLE, $Pathname = "ParentPath", $IDName = "ParentID", $cmd = ""){
		if(!$table){
			$table = $this->Table;
		}
		$textname = 'we_' . $this->Name . '_' . $Pathname;
		$idname = 'we_' . $this->Name . '_' . $IDName;
		$path = $this->$Pathname;
		$myid = $this->$IDName;
		$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['" . $idname . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.forms['we_form'].elements['" . $textname . "'].value");
		$button = we_html_button::create_button("select", "javascript:we_cmd('openDirselector',document.forms['we_form'].elements['" . $idname . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','')");
		return we_html_tools::htmlFormElementTable($this->htmlTextInput($textname, 30, $path, "", ' readonly', "text", $width, 0), g_l('weClass', '[dir]'), "left", "defaultfont", $this->htmlHidden($idname, 0), //$myid
				we_html_tools::getPixel(20, 4), $button);
	}

	protected function formExtension2(){
		return we_html_tools::htmlFormElementTable("<b class='defaultfont'>" . $this->Extension . "</b>", g_l('weClass', '[extension]'));
	}

}
