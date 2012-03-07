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
 * @package    webEdition_class
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
if(defined('WE_TAG_GLOBALS') && !we_isLocalRequest()){
	exit(g_l('alert', '[we_localhost_invalid_request]'));
}

/* the parent class of storagable webEdition classes */

abstract class we_class{
	//constants for retrieving data from DB

	const LOAD_MAID_DB = 0;
	const LOAD_TEMP_DB = 1;
	const LOAD_REVERT_DB = 2; //we_temporaryDocument::revert gibst nicht mehr siehe #5789
	const LOAD_SCHEDULE_DB = 3;

	//constants define where to write document (guess)
	const SUB_DIR_NO = 0;
	const SUB_DIR_YEAR = 1;
	const SUB_DIR_YEAR_MONTH = 2;
	const SUB_DIR_YEAR_MONTH_DAY = 3;

	/* Name of the class => important for reconstructing the class from outside the class */

	var $ClassName = __CLASS__;
	/* In this array are all storagable class variables */
	var $persistent_slots = array();
	/* Name of the Object that was createt from this class */
	var $Name = "";

	/* ID from the database record */
	var $ID = 0;

	/* database table in which the object is stored */
	var $Table = "";

	/* Database Object */
	var $DB_WE;

	/* Flag which is set when the file is not new */
	var $wasUpdate = 0;
	var $InWebEdition = 0;
	var $PublWhenSave = 1;
	var $IsTextContentDoc = false;
	var $LoadBinaryContent = false;
	var $fileExists = 1;
	protected $errMsg = '';

	//Overwrite
	function we_new(){

	}

	//Overwrite
	function we_initSessDat($sessDat){

	}

	/* Constructor */

	function __construct(){
		$this->Name = uniqid();
		array_push($this->persistent_slots, "ClassName", "Name", "ID", "Table", "wasUpdate", "InWebEdition");
		$this->DB_WE = new DB_WE;
	}

	/* Intialize the class. If $sessDat (array) is set, the class will be initialized from this array */

	function init(){
		$this->we_new();
	}

	/* returns the url $in with $we_transaction appended */

	function url($in){
		return $in . ( strstr($in, "?") ? "&" : "?") . "we_transaction=" . $GLOBALS['we_transaction'];
	}

	/* shortcut for print $this->url() */

	function pUrl($in){
		print $this->url($in);
	}

	/* returns the code for a hidden " we_transaction-field" */

	function hiddenTrans(){
		return '<input type="hidden" name="we_transaction" value="' . $GLOBALS['we_transaction'] . '" />';
	}

	/* shortcut for print $this->hiddenTrans() */

	function pHiddenTrans(){
		print $this->hiddenTrans();
	}

	/* must be overwritten by child */

	function saveInSession(&$save){

	}

	###############################

	function hrefRow($intID_elem_Name, $intID, $Path_elem_Name, $path, $attr, $int_elem_Name, $showRadio = false, $int = true, $extraCmd = "", $file = true, $directory = false){

		$out = '<tr>';
		if($showRadio){
			$checked = ($intID_elem_Name && $int) || ((!$intID_elem_Name) && (!$int));

			$out = "<td>" . we_forms::radiobutton(($intID_elem_Name ? 1 : 0), $checked, $int_elem_Name, ((!$intID_elem_Name) ? g_l('tags', "[ext_href]") : g_l('tags', "[int_href]")) . ":&nbsp;", true, "defaultfont", "")
				. "</td>";
		} else{
			$out .= '<input type="hidden" name="' . $int_elem_Name . '" value="' . ($intID_elem_Name ? 1 : 0) . '" />';
		}
		$out .= '			<td>';
		if($intID_elem_Name){
			$out .= '<input type="hidden" name="' . $intID_elem_Name . '" value="' . $intID . '"><input type="text" name="' . $Path_elem_Name . '" value="' . $path . '" ' . $attr . ' readonly="readonly" />';
		} else{
			$out .= '<input' . ($showRadio ? ' onChange="this.form.elements[\'' . $int_elem_Name . '\'][' . ($intID_elem_Name ? 0 : 1) . '].checked=true;"' : '' ) . ' type="text" name="' . $Path_elem_Name . '" value="' . $path . '" ' . $attr . ' />';
		}
		if($intID_elem_Name){
			$trashbut = we_button::create_button("image:btn_function_trash", "javascript:document.we_form.elements['" . $intID_elem_Name . "'].value='';document.we_form.elements['" . $Path_elem_Name . "'].value='';_EditorFrame.setEditorIsHot(true);");
			if(($directory && $file) || $file){
				//javascript:we_cmd('openDocselector',document.forms[0].elements['$intID_elem_Name'].value,'" . FILE_TABLE . "','document.forms[\\'we_form\\'].elements[\\'$intID_elem_Name\\'].value','document.forms[\\'we_form\\'].elements[\\'$Path_elem_Name\\'].value','opener._EditorFrame.setEditorIsHot(true);".($showRadio ? "opener.document.we_form.elements[\'$int_elem_Name\'][0].checked=true;" : "").$extraCmd."','".session_id()."',0,'',".(we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1).",'',".($directory ? 0 : 1).");
				$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['$intID_elem_Name'].value");
				$wecmdenc2 = we_cmd_enc("document.forms['we_form'].elements['$Path_elem_Name'].value");
				$wecmdenc3 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);" . ($showRadio ? "opener.document.we_form.elements['$int_elem_Name'][0].checked=true;" : "") . str_replace('\\', '', $extraCmd));

				$but = we_button::create_button("select", "javascript:we_cmd('openDocselector',document.forms[0].elements['$intID_elem_Name'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "',0,''," . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ",''," . ($directory ? 0 : 1) . ");");
			} else{
				//javascript:we_cmd('openDirselector',document.forms[0].elements['$intID_elem_Name'].value,'" . FILE_TABLE . "','document.forms[\\'we_form\\'].elements[\\'$intID_elem_Name\\'].value','document.forms[\\'we_form\\'].elements[\\'$Path_elem_Name\\'].value','opener._EditorFrame.setEditorIsHot(true);".($showRadio ? "opener.document.we_form.elements[\'$int_elem_Name\'][0].checked=true;" : "").$extraCmd."','".session_id()."',0);
				$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['$intID_elem_Name'].value");
				$wecmdenc2 = we_cmd_enc("document.forms['we_form'].elements['$Path_elem_Name'].value");
				$wecmdenc3 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);" . ($showRadio ? "opener.document.we_form.elements['$int_elem_Name'][0].checked=true;" : "") . str_replace('\\', '', $extraCmd));
				$but = we_button::create_button("select", "javascript:we_cmd('openDirselector',document.forms[0].elements['$intID_elem_Name'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "',0);");
			}
		} else{
			$trashbut = we_button::create_button("image:btn_function_trash", "javascript:document.we_form.elements['" . $Path_elem_Name . "'].value='';_EditorFrame.setEditorIsHot(true);");
			if(($directory && $file) || $file){

				//javascript:we_cmd('browse_server','document.forms[0].elements[\\'$Path_elem_Name\\'].value','".(($directory && $file) ? "filefolder" : "")."',document.forms[0].elements['$Path_elem_Name'].value,'if (opener.opener != null){opener.opener._EditorFrame.setEditorIsHot(true);}else{opener._EditorFrame.setEditorIsHot(true);}".($showRadio ? "opener.document.we_form.elements[\'$int_elem_Name\'][1].checked=true;" : "")."')
				$wecmdenc1 = we_cmd_enc("document.forms[0].elements['$Path_elem_Name'].value");
				$wecmdenc4 = we_cmd_enc("if (opener.opener != null){opener.opener._EditorFrame.setEditorIsHot(true);}else{opener._EditorFrame.setEditorIsHot(true);}" . ($showRadio ? "opener.document.we_form.elements['$int_elem_Name'][1].checked=true;" : ""));
				$but = we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ?
					we_button::create_button("select", "javascript:we_cmd('browse_server','" . $wecmdenc1 . "','" . (($directory && $file) ? "filefolder" : "") . "',document.forms[0].elements['$Path_elem_Name'].value,'" . $wecmdenc4 . "')") :
					"";
			} else{
				//javascript:formFileChooser('browse_server','document.we_form.elements[\\'$IDName\\'].value','$filter',document.we_form.elements['$IDName'].value,'$cmd');
				$wecmdenc1 = we_cmd_enc("document.forms[0].elements['$Path_elem_Name'].value");
				$wecmdenc4 = we_cmd_enc("if (opener.opener != null){opener.opener._EditorFrame.setEditorIsHot(true);}else{opener._EditorFrame.setEditorIsHot(true);}" . ($showRadio ? "opener.document.we_form.elements['$int_elem_Name'][1].checked=true;" : ""));
				$but = we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ?
					we_button::create_button("select", "javascript:we_cmd('browse_server','" . $wecmdenc1 . "','folder',document.forms[0].elements['$Path_elem_Name'].value,'" . $wecmdenc4 . "')") :
					"";
			}
		}

		$out .='</td>
			<td>' . we_html_tools::getPixel(6, 4) . '</td>
			<td>' . $but . '</td>
			<td>' . we_html_tools::getPixel(5, 2) . '</td>
			<td>' . $trashbut . '</td>
		</tr>
';
		return $out;
	}

	/* creates a text-input field for entering Data that will be stored at the $elements Array */

	function formInput($name, $size = 25, $type = "txt"){
		return $this->formTextInput($type, $name, (g_l('weClass', '[' . $name . ']') ? g_l('weClass', '[' . $name . ']') : $name), $size);
	}

	/* creates a color field. when user clicks, a colorchooser opens. Data that will be stored at the $elements Array */

	function formColor($width, $name, $size = 25, $type = "txt", $height = 18, $isTag = false){
		$value = $this->getElement($name);
		if(!$isTag){
			$width -= 4;
		}
		$formname = "we_" . $this->Name . "_" . $type . "[$name]";
		$out = $this->htmlHidden($formname, $this->getElement($name)) . '<table cellpadding="0" cellspacing="0" border="1"><tr><td' . ($value ? (' bgcolor="' . $value . '"') : '') . '><a href="javascript:setScrollTo();we_cmd(\'openColorChooser\',\'' . $formname . '\',document.we_form.elements[\'' . $formname . '\'].value);">' . we_html_tools::getPixel($width, $height) . '</a></td></tr></table>';
		return g_l('weClass', '[' . $name . ']') !== false ? $this->htmlFormElementTable($out, g_l('weClass', '[' . $name . ']')) : $out;
	}

	/* creates a select field for entering Data that will be stored at the $elements Array */

	function formSelectElement($name, $values, $type = "txt", $size = 1){
		$out = '<select class="defaultfont" name="we_' . $this->Name . "_" . $type . "[$name]" . '" size="' . $size . '">' . "\n";
		$value = $this->getElement($name);
		reset($values);
		while(list($val, $txt) = each($values)) {
			$out .= '<option value="' . $val . '"' . (($val == $value) ? " selected" : "") . '>' . $txt . "</option>\n";
		}
		$out .= "</select>\n";
		return $this->htmlFormElementTable($out, g_l('weClass', '[' . $name . ']'));
	}

	function formTextInput($elementtype, $name, $text, $size = 24, $maxlength = "", $attribs = "", $textalign = "left", $textclass = "defaultfont"){
		if(!$elementtype)
			$ps = $this->$name;
		return $this->htmlFormElementTable($this->htmlTextInput(($elementtype ? ("we_" . $this->Name . "_" . $elementtype . "[$name]") : ("we_" . $this->Name . "_" . $name)), $size, ($elementtype ? $this->getElement($name) : $ps), $maxlength, $attribs), $text, $textalign, $textclass);
	}

	function formInputField($elementtype, $name, $text, $size = 24, $width, $maxlength = "", $attribs = "", $textalign = "left", $textclass = "defaultfont"){
		if(!$elementtype){
			$ps = $this->$name;
		}
		return $this->htmlFormElementTable($this->htmlTextInput(($elementtype ? ("we_" . $this->Name . "_" . $elementtype . "[$name]") : ("we_" . $this->Name . "_" . $name)), $size, ($elementtype && $this->getElement($name) != "" ? $this->getElement($name) : (isset($GLOBALS["meta"][$name]) ? $GLOBALS["meta"][$name]["default"] : (isset($ps) ? $ps : "") )), $maxlength, $attribs, "text", $width), $text, $textalign, $textclass);
	}

	function formPasswordInput($elementtype, $name, $text, $size = 24, $maxlength = "", $attribs = "", $textalign = "left", $textclass = "defaultfont"){
		return $this->htmlFormElementTable($this->htmlPasswordInput(($elementtype ? ("we_" . $this->Name . "_" . $elementtype . "[$name]") : ("we_" . $this->Name . "_" . $name)), $size, "", $maxlength, $attribs), $text, $textalign, $textclass);
	}

	function formTextArea($elementtype, $name, $text, $rows = 10, $cols = 30, $attribs = "", $textalign = "left", $textclass = "defaultfont"){
		if(!$elementtype)
			$ps = $this->$name;
		return $this->htmlFormElementTable($this->htmlTextArea(($elementtype ? ("we_" . $this->Name . "_" . $elementtype . "[$name]") : ("we_" . $this->Name . "_" . $name)), $rows, $cols, ($elementtype ? $this->getElement($name) : $ps), $attribs), $text, $textalign, $textclass);
	}

	function formSelect($elementtype, $name, $table, $val, $txt, $text, $sqlTail = "", $size = 1, $selectedIndex = "", $multiple = false, $attribs = "", $textalign = "left", $textclass = "defaultfont", $precode = "", $postcode = "", $firstEntry = ""){
		$vals = array();
		if($firstEntry)
			$vals[$firstEntry[0]] = $firstEntry[1];
		$this->DB_WE->query("SELECT * FROM $table $sqlTail");
		while($this->DB_WE->next_record()) {
			$v = $this->DB_WE->f($val);
			$t = $this->DB_WE->f($txt);
			$vals[$v] = $t;
		}

		if(!$elementtype)
			$ps = $this->$name;
		$pop = $this->htmlSelect(($elementtype ? ("we_" . $this->Name . "_" . $elementtype . "[$name]") : ("we_" . $this->Name . "_" . $name)), $vals, $size, ($elementtype ? $this->getElement($name) : $ps), $multiple, $attribs);
		return $this->htmlFormElementTable(($precode ? $precode : "") . $pop . ($postcode ? $postcode : ""), $text, $textalign, $textclass);
	}

	function formSelectFromArray($elementtype, $name, $vals, $text, $size = 1, $selectedIndex = "", $multiple = false, $attribs = "", $textalign = "left", $textclass = "defaultfont", $precode = "", $postcode = "", $firstEntry = ""){

		if(!$elementtype)
			$ps = $this->$name;
		$pop = $this->htmlSelect2(($elementtype ? ("we_" . $this->Name . "_" . $elementtype . "[$name]") : ("we_" . $this->Name . "_" . $name)), $vals, $size, ($elementtype ? $this->getElement($name) : $ps), $multiple, $attribs);
		return $this->htmlFormElementTable(($precode ? $precode : "") . $pop . ($postcode ? $postcode : ""), $text, $textalign, $textclass);
	}

	function htmlTextInput($name, $size = 24, $value = "", $maxlength = "", $attribs = "", $type = "text", $width = "0", $height = "0"){
		return we_html_tools::htmlTextInput($name, $size, $value, $maxlength, $attribs, $type, $width, $height);
	}

	function htmlHidden($name, $value = "", $params = null){
		return '<input type="hidden" name="' . trim($name) . '" value="' . htmlspecialchars($value) . '" ' . $params . ' />';
	}

	function htmlPasswordInput($name, $size = 24, $value = "", $maxlength = "", $attribs = ""){
		return $this->htmlTextInput($name, $size, $value, $maxlength, $attribs, "password");
	}

	function htmlTextArea($name, $rows = 10, $cols = 30, $value = "", $attribs = ""){
		return '<textarea class="defaultfont" name="' . trim($name) . '" rows="' . abs($rows) . '" cols="' . abs($cols) . '"' . ($attribs ? " $attribs" : '') . '>' . ($value ? (htmlspecialchars($value)) : '') . '</textarea>';
	}

	function htmlRadioButton($name, $value, $checked = false, $attribs = "", $text = "", $textalign = "left", $textclass = "defaultfont", $type = "radio", $width = ""){
		$v = $value;
		return ( $text ?
				('<table cellpadding="0" cellspacing="0" border="0"' . ($width ? " width=$width" : "") . '><tr>' . (($textalign == "left") ?
					('<td class="' . $textclass . '">' . $text . '&nbsp;</td><td>') :
					"<td>")
				) :
				""
			) . '<input type="' . trim($type) . '" name="' . trim($name) . '" value="' . $v . '"' . ($attribs ? " $attribs" : '') . ($checked ? " checked" : "") . ' />' .
			( $text ?
				( (($textalign == "right") ?
					('</td><td class="' . $textclass . '">&nbsp;' . $text . '</td>') :
					'</td>'
				) . '</tr></table>'
				) :
				""
			);
	}

	function htmlCheckBox($name, $value, $checked = false, $attribs = "", $text = "", $textalign = "left", $textclass = "defaultfont"){
		$v = $value;
		$type = "checkbox";
		return ( $text ?
				('<table cellpadding="0" cellspacing="0" border="0"><tr>' . (($textalign == "left") ?
					('<td class="' . $textclass . '">' . $text . '&nbsp;</td><td>') :
					"<td>")
				) :
				""
			) . '<input type="' . trim($type) . '" name="' . trim($name) . '" value="' . $v . '"' . ($attribs ? " $attribs" : '') . ($checked ? " checked" : "") . ' />' .
			( $text ?
				( (($textalign == "right") ?
					('</td><td class="' . $textclass . '">&nbsp;' . $text . '</td>') :
					'</td>'
				) . '</tr></table>'
				) :
				""
			);
	}

	function htmlSelect($name, $values, $size = 1, $selectedIndex = '', $multiple = false, $attribs = '', $compare = 'value', $width = 0){
		if(is_array($values)){
			reset($values);
		} else{
			$values = array();
		}
		$ret = '<select id="' . trim($name) . '" class="weSelect defaultfont" name="' . trim($name) . '" size="' . abs($size) . '"' . ($multiple ? ' multiple="multiple"' : '') . ($attribs ? " $attribs" : "") . ($width ? ' style="width: ' . $width . 'px"' : '') . '>';
		$selIndex = explode(',', $selectedIndex);
		foreach($values as $value => $text){
			$ret .= '<option value="' . htmlspecialchars($value) . '"' . (in_array((($compare == 'value') ? $value : $text), $selIndex) ? ' selected="selected"' : '') . '>' . $text . '</option>';
		}
		$ret .= '</select>';
		return $ret;
	}

	// this function doesn't split selectedIndex
	function htmlSelect2($name, $values, $size = 1, $selectedIndex = "", $multiple = false, $attribs = "", $compare = "value", $width = ""){
		reset($values);
		$ret = '<select id="' . trim($name) . '" class="weSelect defaultfont" name="' . trim($name) . '" size="' . abs($size) . '"' . ($multiple ? " multiple" : "") . ($attribs ? " $attribs" : "") . ($width ? ' style="width: ' . $width . 'px"' : '') . '>' . "\n";
		while(list($value, $text) = each($values)) {
			$ret .= '<option value="' . htmlspecialchars($value) . '"' . (($selectedIndex == (($compare == "value") ? $value : $text)) ? " selected=\"selected\"" : "") . '>' . $text . "</option>\n";
		}
		$ret .= "</select>";
		return $ret;
	}

	function htmlFormElementTable($element, $text, $textalign = "left", $textclass = "defaultfont", $col2 = "", $col3 = "", $col4 = "", $col5 = "", $col6 = ""){
		return we_html_tools::htmlFormElementTable($element, $text, $textalign, $textclass, $col2, $col3, $col4, $col5, $col6);
	}

	############## new fns
	/* creates a select field for entering Data that will be stored at the $elements Array */

	function formSelectElement2($width, $name, $values, $type = "txt", $size = 1, $attribs = ""){
		$out = '<select class="defaultfont" name="we_' . $this->Name . "_" . $type . "[$name]" . '" size="' . $size . '"' . ($width ? ' style="width: ' . $width . 'px"' : '') . ($attribs ? " $attribs" : '') . '>' . "\n";
		$value = $this->getElement($name);
		reset($values);
		foreach($values as $val => $txt){
			$out .= '<option value="' . $val . '"' . (($val == $value) ? " selected" : "") . '>' . $txt . "</option>\n";
		}
		$out .= '</select>';
		return $this->htmlFormElementTable($out, g_l('weClass', '[' . $name . ']'));
	}

	function formInput2($width, $name, $size = 25, $type = "txt", $attribs = ""){
		return $this->formInputField($type, $name, (g_l('weClass', '[' . $name . ']') != false ? g_l('weClass', '[' . $name . ']') : $name), $size, $width, "", $attribs);
	}

	/* creates a text-input field for entering Data that will be stored at the $elements Array and shows information from another Element */

	function formInputInfo2($width, $name, $size, $type = "txt", $attribs = "", $infoname){
		$info = $this->getElement($infoname);
		$infotext = " (" . (g_l('weClass', '[' . $infoname . ']') != false ? g_l('weClass', '[' . $infoname . ']') : $infoname) . ": " . $info . ")";
		return $this->formInputField($type, $name, (g_l('weClass', '[' . $name . ']') !== false ? g_l('weClass', '[' . $name . ']') : $name) . $infotext, $size, $width, "", $attribs);
	}

	function formSelect2($elementtype, $width, $name, $table, $val, $txt, $text, $sqlTail = "", $size = 1, $selectedIndex = "", $multiple = false, $onChange = "", $attribs = "", $textalign = "left", $textclass = "defaultfont", $precode = "", $postcode = "", $firstEntry = "", $gap = 20){
		$vals = array();
		if($firstEntry)
			$vals[$firstEntry[0]] = $firstEntry[1];
		$this->DB_WE->query("SELECT * FROM " . $this->DB_WE->escape($table) . " $sqlTail");
		while($this->DB_WE->next_record()) {
			$v = $this->DB_WE->f($val);
			$t = $this->DB_WE->f($txt);
			$vals[$v] = $t;
		}
		$myname = $elementtype ? ("we_" . $this->Name . "_" . $elementtype . "[$name]") : ("we_" . $this->Name . "_" . $name);


		if($multiple){
			$onChange.= ";var we_sel='';for(i=0;i<this.options.length;i++){if(this.options[i].selected){we_sel += (this.options[i].value + ',');};};if(we_sel){we_sel=we_sel.substring(0,we_sel.length-1)};this.form.elements['" . $myname . "'].value=we_sel;";
			if(!$elementtype)
				$ps = $this->$name;
			$pop = $this->htmlSelect($myname . "Tmp", $vals, $size, ($elementtype ? $this->getElement($name) : $ps), $multiple, "onChange=\"$onChange\" " . $attribs, "value", $width);

			if($precode || $postcode){
				$pop = '<table border="0" cellpadding="0" cellspacing="0"><tr>' . ($precode ? ("<td>$precode</td><td>" . we_html_tools::getPixel($gap, 2) . "</td>") : "") . '<td>' . $pop . '</td>' . ($postcode ? ("<td>" . we_html_tools::getPixel($gap, 2) . "</td><td>$postcode</td>") : "") . '</tr></table>';
			}

			return $this->htmlHidden($myname, $selectedIndex) . $this->htmlFormElementTable($pop, $text, $textalign, $textclass);
		} else{
			if(!$elementtype)
				$ps = $this->$name;
			$pop = $this->htmlSelect($myname, $vals, $size, ($elementtype ? $this->getElement($name) : $ps), $multiple, "onChange=\"$onChange\" " . $attribs, "value", $width);
			if($precode || $postcode){
				$pop = '<table border="0" cellpadding="0" cellspacing="0"><tr>' . ($precode ? ("<td>$precode</td><td>" . we_html_tools::getPixel($gap, 2) . "</td>") : "") . '<td>' . $pop . '</td>' . ($postcode ? ("<td>" . we_html_tools::getPixel($gap, 2) . "</td><td>$postcode</td>") : "") . '</tr></table>';
			}
			return $this->htmlFormElementTable($pop, $text, $textalign, $textclass);
		}
	}

	function formSelect4($elementtype, $width, $name, $table, $val, $txt, $text, $sqlTail = "", $size = 1, $selectedIndex = "", $multiple = false, $onChange = "", $attribs = "", $textalign = "left", $textclass = "defaultfont", $precode = "", $postcode = "", $firstEntry = ""){
		$vals = array();
		if($firstEntry)
			$vals[$firstEntry[0]] = $firstEntry[1];
		$this->DB_WE->query("SELECT * FROM " . $this->DB_WE->escape($table) . " $sqlTail");
		while($this->DB_WE->next_record()) {
			$v = $this->DB_WE->f($val);
			$t = $this->DB_WE->f($txt);
			$vals[$v] = $t;
		}
		$myname = "we_" . $this->Name . "_" . $name;


		if(!$elementtype)
			$ps = $this->$name;
		$pop = $this->htmlSelect($myname, $vals, $size, $selectedIndex, $multiple, "onChange=\"$onChange\" " . $attribs, "value", $width);
		return $this->htmlFormElementTable(($precode ? $precode : "") . $pop . ($postcode ? $postcode : ""), $text, $textalign, $textclass);
	}

##### NEWSTUFF ####
# public ##################

	function initByID($ID, $Table = "", $from = we_class::LOAD_MAID_DB){
		if($Table == ""){
			$Table = FILE_TABLE;
		}
		$this->ID = intval($ID);
		$this->Table = $Table;
		$this->we_load($from);
		$GLOBALS["we_ID"] = $ID; //FIXME: look if we need this !!
		$GLOBALS["we_Table"] = $Table;
		// init Customer Filter !!!!
		if(isset($this->documentCustomerFilter) && defined('CUSTOMER_TABLE')){
			$this->initWeDocumentCustomerFilterFromDB();
		}
	}

	/**
	 * inits weDocumentCustomerFilter from db regarding the modelId
	 * is called from "we_textContentDocument::we_load"
	 * @see we_textContentDocument::we_load
	 */
	function initWeDocumentCustomerFilterFromDB(){
		$this->documentCustomerFilter = weDocumentCustomerFilter::getFilterOfDocument($this);
	}

	function we_load($from = we_class::LOAD_MAID_DB){
		$this->i_getPersistentSlotsFromDB();
	}

	function we_save($resave = 0){
		$this->wasUpdate = $this->ID ? 1 : 0;
		return $this->i_savePersistentSlotsToDB();
	}

	function we_publish($DoNotMark = false, $saveinMainDB = true){
		return true; // overwrite
	}

	function we_unpublish($DoNotMark = false){
		return true; // overwrite
	}

	function we_republish(){
		return true;
	}

	function we_delete(){
		if(defined('LANGLINK_SUPPORT') && LANGLINK_SUPPORT){
			switch($this->ClassName){
				case 'we_objectFile':
					$deltype = 'tblObjectFile';
					break;
				case 'we_webEditionDocument':
					$deltype = 'tblFile';
					break;
				case 'we_docTypes':
					$deltype = 'tblDocTypes';
					break;
				default:
					$deltype = '';
			}
			$this->DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . " WHERE DocumentTable='" . $deltype . "' AND DID=" . intval($this->ID));
			$this->DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . " WHERE DocumentTable='" . $deltype . "' AND LDID=" . intval($this->ID));
		}
		return $this->DB_WE->query('DELETE FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID=' . intval($this->ID));
	}

# private ###################

	protected function i_setElementsFromHTTP(){

		// do not set REQUEST VARS into the document
		if(($_REQUEST['we_cmd'][0] == "switch_edit_page" && isset($_REQUEST['we_cmd'][3]))
			|| ($_REQUEST['we_cmd'][0] == "save_document" && isset($_REQUEST['we_cmd'][7]) && $_REQUEST['we_cmd'][7] == "save_document")){
			return true;
		}
		if(sizeof($_REQUEST)){
			foreach($_REQUEST as $n => $v){
				if(preg_match('#^we_' . $this->Name . '_([^\[]+)$#', $n, $regs)){
					if(in_array($regs[1], $this->persistent_slots)){
						$this->$regs[1] = $v;
					}
				}
			}
		}
	}

	function i_getPersistentSlotsFromDB($felder = '*'){
		$fields = getHash('SELECT ' . $felder . ' FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID=' . intval($this->ID), $this->DB_WE);
		if(count($fields)){
			foreach($fields as $k => $v){
				if($k && in_array($k, $this->persistent_slots)){
					$this->{$k} = $v;
				}
			}
		} else{
			$this->fileExists = 0;
		}
	}

	function i_fixCSVPrePost($in){
		if($in){
			if(substr($in, 0, 1) != ","){
				$in = "," . $in;
			}
			if(substr($in, -1) != ","){
				$in .= ",";
			}
		}
		return $in;
	}

	function i_savePersistentSlotsToDB($felder = ""){
		$tableInfo = $this->DB_WE->metadata($this->Table);
		$feldArr = $felder ? makeArrayFromCSV($felder) : $this->persistent_slots;
		$fields = array();
		foreach($tableInfo as $info){

			$fieldName = $info["name"];
			if(in_array($fieldName, $feldArr)){
				$val = isset($this->$fieldName) ? $this->$fieldName : '';

				if($fieldName == "Category"){ // Category-Fix!
					$val = $this->i_fixCSVPrePost($val);
				}
				if($fieldName != "ID")
					$fields[$fieldName] = $val;
			}
		}
		if(count($fields)){
			$where = ($this->wasUpdate) ? ' WHERE ID=' . intval($this->ID) : '';
			$ret = (bool) ($this->DB_WE->query(($this->wasUpdate ? 'UPDATE ' : 'INSERT INTO ') . $this->DB_WE->escape($this->Table) . ' SET ' . we_database_base::arraySetter($fields) . $where));
			$this->ID = ($this->wasUpdate) ? $this->ID : $this->DB_WE->getInsertId();
			return $ret;
		}
		return false;
	}

	function i_descriptionMissing(){
		return false;
	}

	function setDocumentControlElements(){
		//	function is overwritten in we_webEditionDocument
	}

	function executeDocumentControlElements(){
		//	function is overwritten in we_webEditionDocument
	}

	function isValidEditPage($editPageNr){

		if(is_array($this->EditPageNrs)){
			return in_array($editPageNr, $this->EditPageNrs);
		}
		return false;
	}

	protected function updateRemoteLang($db, $id, $lang, $type){
		//overwrite if needed
	}

	function setLanguageLink($LangLinkArray, $type, $isfolder = false, $isobject = false){
		$db = new DB_WE;
		if(is_array($LangLinkArray)){
			$q = 'SELECT * FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $type . '" AND DID=' . intval($this->ID);
			$orig = array();
			$this->DB_WE->query($q);
			while($this->DB_WE->next_record()) {
				$orig[] = $this->DB_WE->Record;
			}
			$max = count($orig);
			for($j = 0; $j < $max; $j++){
				$q = 'SELECT * FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $type . '" AND DID=' . intval($orig[$j]['LDID']);
				$this->DB_WE->query($q);
				while($this->DB_WE->next_record()) {
					$orig[] = $this->DB_WE->Record;
				}
			}
			foreach($LangLinkArray as $locale => $LDID){
				if(($ID = f("SELECT ID FROM " . LANGLINK_TABLE . " WHERE DocumentTable='" . $type . "' AND DID=" . intval($this->ID) . " AND Locale='" . $locale . "' AND IsObject=" . intval($isobject), 'ID', $this->DB_WE))){
					$q = "UPDATE " . LANGLINK_TABLE . " SET LDID=" . intval($LDID) . ",DLocale='" . $this->Language . "' WHERE ID=" . intval($ID) . ' AND DocumentTable="' . $type . '"';
					$this->DB_WE->query($q);
				} else{
					if($locale != $this->Language){
						if($LDID > 0){
							$q = "INSERT INTO " . LANGLINK_TABLE . " SET DID=" . intval($this->ID) . ",DLocale='" . $this->Language . "',IsFolder=" . intval($isfolder) . ", IsObject=" . intval($isobject) . ", LDID=" . intval($LDID) . ", Locale='" . $locale . "', DocumentTable='" . $type . "';";
							$this->DB_WE->query($q);
						}
					}
				}
				if(( (defined('LANGLINK_SUPPORT_BACKLINKS') && LANGLINK_SUPPORT_BACKLINKS) || (defined('LANGLINK_SUPPORT_RECURSIVE') && LANGLINK_SUPPORT_RECURSIVE) ) && !$isfolder && $LDID && $LDID != $this->ID){
					$q = '';
					if($ID = f("SELECT ID FROM " . LANGLINK_TABLE . " WHERE DocumentTable='" . $type . "' AND DID=" . intval($LDID) . " AND Locale='" . $this->Language . "' AND IsObject=" . intval($isobject), 'ID', $this->DB_WE)){
						if($LDID > 0){
							$q = "UPDATE " . LANGLINK_TABLE . " SET DID=" . intval($LDID) . ", DLocale='" . $locale . "', LDID=" . intval($this->ID) . ",Locale='" . $this->Language . "' WHERE ID=" . intval($ID) . ' AND DocumentTable="' . $type . '"';
						}
						if($LDID < 0){
							$q = "UPDATE " . LANGLINK_TABLE . " SET DID=" . intval($LDID) . ", DLocale='" . $locale . "', LDID='0',Locale='" . $this->Language . "' WHERE ID=" . intval($ID) . ' AND DocumentTable="' . $type . '"';
						}
					} else{
						if($LDID > 0){
							$q = "INSERT INTO " . LANGLINK_TABLE . " SET DID=" . intval($LDID) . ", DLocale='" . $locale . "', LDID=" . intval($this->ID) . ", Locale='" . $this->Language . "', IsObject=" . intval($isobject) . ", DocumentTable='" . $type . "';";
						}
					}
					if($q){
						$this->DB_WE->query($q);
					}
				}
				if(( (defined('LANGLINK_SUPPORT_BACKLINKS') && LANGLINK_SUPPORT_BACKLINKS) || (defined('LANGLINK_SUPPORT_RECURSIVE') && LANGLINK_SUPPORT_RECURSIVE) ) && !$isfolder && $LDID < 0 && $LDID != $this->ID){
					if($LDID > 0){
						$this->DB_WE->query("REPLACE INTO " . LANGLINK_TABLE . " SET DID=" . intval($LDID) . ", DLocale='" . $locale . "', LDID=" . intval($this->ID) . ", Locale='" . $this->Language . "', IsObject=" . intval($isobject) . ", DocumentTable='" . $type . "'");
					}
				}
			}
			if(defined('LANGLINK_SUPPORT_RECURSIVE') && LANGLINK_SUPPORT_RECURSIVE && !$isfolder){
				foreach($LangLinkArray as $locale => $LDID){
					if($LDID > 0){
						$rows = array();
						$this->DB_WE->query("SELECT * FROM " . LANGLINK_TABLE . " WHERE  DID=" . intval($this->ID) . "  AND DocumentTable='" . $type . "' AND IsObject=" . intval($isobject));
						while($this->DB_WE->next_record()) {
							$rows[] = $this->DB_WE->Record;
						}
						if(count($rows) > 1){
							for($i = 0; $i < count($rows) - 1; $i++){
								if($rows[$i]['LDID'] && $rows[$i + 1]['LDID']){
									$this->DB_WE->query("REPLACE INTO " . LANGLINK_TABLE . " SET DID=" . intval($rows[$i]['LDID']) . ", DLocale='" . $rows[$i]['Locale'] . "', LDID=" . intval($rows[$i + 1]['LDID']) . ", Locale='" . $rows[$i + 1]['Locale'] . "', IsObject=" . intval($isobject) . ", DocumentTable='" . $type);
									$this->DB_WE->query("REPLACE INTO " . LANGLINK_TABLE . " SET DID=" . intval($rows[$i + 1]['LDID']) . ", DLocale=" . $rows[$i + 1]['Locale'] . ", LDID=" . intval($rows[$i]['LDID']) . ", Locale='" . $rows[$i]['Locale'] . "', IsObject=" . intval($isobject) . ", DocumentTable='" . $type);
								}
							}
						}
					}
					if($LDID < 0){
						foreach($orig as $origrow){
							if($origrow['DLocale'] == $locale){
								$q = "SELECT ID FROM " . LANGLINK_TABLE . " WHERE  DID=" . intval($origrow['DID']) . " AND DLocale='" . $locale . "' AND DocumentTable='" . $type . "' AND IsObject=" . intval($isobject);
								$this->DB_WE->query($q);
								while($this->DB_WE->next_record()) {
									$delRowID = $this->DB_WE->Record['ID'];
									$qd = "UPDATE " . LANGLINK_TABLE . " SET LDID='0' WHERE ID=" . intval($delRowID) . ' AND DocumentTable="' . $type . '"';
									$db->query($qd);
								}
							}
							if($origrow['Locale'] == $locale){
								$q = "SELECT ID FROM " . LANGLINK_TABLE . " WHERE  LDID=" . intval($origrow['LDID']) . " AND Locale='" . $locale . "' AND DocumentTable='" . $type . "' AND IsObject=" . intval($isobject);
								$this->DB_WE->query($q);
								while($this->DB_WE->next_record()) {
									$delRowID = $this->DB_WE->Record['ID'];
									$qd = "UPDATE " . LANGLINK_TABLE . " SET LDID='0' WHERE ID=" . intval($delRowID) . ' AND DocumentTable="' . $type . '"';
									$db->query($qd);
								}
							}
						}
					}
				}
			}
		}
	}

	/*	 * returns error-messages recorded during an operation, currently only save is used */

	function getErrMsg(){
		return ($this->errMsg != '' ? '\n' . str_replace("\n", '\n', $this->errMsg) : '');
	}

}
