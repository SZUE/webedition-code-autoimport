<?php
/**
 * webEdition CMS
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



if(!isset($GLOBALS["WE_IS_DYN"])){
	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_global.inc.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/we_class.inc.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/tags.inc.php");
	include_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_classes/html/we_forms.inc.php");
	include_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_classes/html/we_button.inc.php");
}
if(defined("WE_TAG_GLOBALS") && !we_isLocalRequest()) {
	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/alert.inc.php");
	exit($l_alert["we_localhost_invalid_request"]);
}

/* the parent class of storagable webEdition classes */
class we_class
{

	######################################################################################################################################################
	##################################################################### Variables ######################################################################
	######################################################################################################################################################
	/* Name of the class => important for reconstructing the class from outside the class */
	var $ClassName="we_class";
	/* In this array are all storagable class variables */
	var $persistent_slots=array();
	/* Name of the Object that was createt from this class */
	var $Name="";

	/* ID from the database record */
	var $ID=0;

	/* database table in which the object is stored */
	var $Table="";

	/* Database Object */
	var $DB_WE;

	/* Flag which is set when the file is not new */
	var $wasUpdate=0;

	var $InWebEdition = 0;

	var $PublWhenSave = 1;

	var $IsTextContentDoc = false;

	var $LoadBinaryContent = false;

	var $fileExists = 1;
	protected $errMsg='';

	######################################################################################################################################################
	##################################################################### FUNCTIONS ######################################################################
	######################################################################################################################################################

	/* Constructor */
	function we_class(){
		$this->Name = md5(uniqid(rand()));
		array_push($this->persistent_slots,"ClassName","Name","ID","Table","wasUpdate","InWebEdition");
		$this->DB_WE = new DB_WE;
	}

	/* Intialize the class. If $sessDat (array) is set, the class will be initialized from this array */
	function init(){
		$this->we_new();
	}

	/* returns the url $in with $we_transaction appended */
	function url($in){
		global $we_transaction;
		return $in . ( strstr($in,"?") ? "&" : "?") . "we_transaction=" . $we_transaction;
	}

	/* shortcut for print $this->url() */
	function pUrl($in){
		print $this->url($in);
	}

	/* returns the code for a hidden " we_transaction-field" */
	function hiddenTrans(){
		global $we_transaction;
		return '<input type="hidden" name="we_transaction" value="'.$we_transaction.'" />';
	}

	/* shortcut for print $this->hiddenTrans() */
	function pHiddenTrans(){
		print $this->hiddenTrans();
	}


	/* must be overwritten by child */
	function saveInSession(&$save){
	}


	###############################

	function hrefRow($intID_elem_Name,$intID,$Path_elem_Name,$path,$attr,$int_elem_Name,$showRadio=false,$int=true,$extraCmd="",$file=true, $directory=false){

		$we_button = new we_button();

		$out = '		<tr>
';
		if($showRadio){
			$checked = ($intID_elem_Name && $int) || ((!$intID_elem_Name) && (!$int)) ;

			$out = "<td>" . we_forms::radiobutton( ($intID_elem_Name ? 1 : 0), $checked, $int_elem_Name, ((!$intID_elem_Name) ?  $GLOBALS["l_tags"]["ext_href"] : $GLOBALS["l_tags"]["int_href"]) .":&nbsp;", true, "defaultfont", "")
			. "</td>";

		}else{
			$out .= '<input type="hidden" name="'.$int_elem_Name.'" value="'.($intID_elem_Name ? 1 : 0).'" />';
		}
		$out .= '			<td>';
		if($intID_elem_Name){
			$out .= '<input type="hidden" name="'.$intID_elem_Name.'" value="'.$intID.'"><input type="text" name="'.$Path_elem_Name.'" value="'.$path.'" '.$attr.' readonly="readonly" />';
		}else{
			$out .= '<input'.($showRadio ? ' onChange="this.form.elements[\''.$int_elem_Name.'\']['.($intID_elem_Name ? 0 : 1).'].checked=true;"' : '' ).' type="text" name="'.$Path_elem_Name.'" value="'.$path.'" '.$attr.' />';
		}
		if($intID_elem_Name){
			$trashbut = $we_button->create_button("image:btn_function_trash", "javascript:document.we_form.elements['".$intID_elem_Name."'].value='';document.we_form.elements['" . $Path_elem_Name . "'].value='';_EditorFrame.setEditorIsHot(true);");
			if(($directory && $file) || $file){
				//javascript:we_cmd('openDocselector',document.forms[0].elements['$intID_elem_Name'].value,'" . FILE_TABLE . "','document.forms[\\'we_form\\'].elements[\\'$intID_elem_Name\\'].value','document.forms[\\'we_form\\'].elements[\\'$Path_elem_Name\\'].value','opener._EditorFrame.setEditorIsHot(true);".($showRadio ? "opener.document.we_form.elements[\'$int_elem_Name\'][0].checked=true;" : "").$extraCmd."','".session_id()."',0,'',".(we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1).",'',".($directory ? 0 : 1).");
				$wecmdenc1= we_cmd_enc("document.forms['we_form'].elements['$intID_elem_Name'].value");
				$wecmdenc2= we_cmd_enc("document.forms['we_form'].elements['$Path_elem_Name'].value");
				$wecmdenc3= we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);".($showRadio ? "opener.document.we_form.elements['$int_elem_Name'][0].checked=true;" : "").str_replace('\\','',$extraCmd)."");

				$but      = $we_button->create_button("select", "javascript:we_cmd('openDocselector',document.forms[0].elements['$intID_elem_Name'].value,'" . FILE_TABLE . "','".$wecmdenc1."','".$wecmdenc2."','".$wecmdenc3."','".session_id()."',0,'',".(we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1).",'',".($directory ? 0 : 1).");");
			}else{
				//javascript:we_cmd('openDirselector',document.forms[0].elements['$intID_elem_Name'].value,'" . FILE_TABLE . "','document.forms[\\'we_form\\'].elements[\\'$intID_elem_Name\\'].value','document.forms[\\'we_form\\'].elements[\\'$Path_elem_Name\\'].value','opener._EditorFrame.setEditorIsHot(true);".($showRadio ? "opener.document.we_form.elements[\'$int_elem_Name\'][0].checked=true;" : "").$extraCmd."','".session_id()."',0);
				$wecmdenc1= we_cmd_enc("document.forms['we_form'].elements['$intID_elem_Name'].value");
				$wecmdenc2= we_cmd_enc("document.forms['we_form'].elements['$Path_elem_Name'].value");
				$wecmdenc3= we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);".($showRadio ? "opener.document.we_form.elements['$int_elem_Name'][0].checked=true;" : "").str_replace('\\','',$extraCmd)."");
				$but      = $we_button->create_button("select", "javascript:we_cmd('openDirselector',document.forms[0].elements['$intID_elem_Name'].value,'" . FILE_TABLE . "','".$wecmdenc1."','".$wecmdenc2."','".$wecmdenc3."','".session_id()."',0);");
			}
		}else{
			$trashbut = $we_button->create_button("image:btn_function_trash", "javascript:document.we_form.elements['".$Path_elem_Name."'].value='';_EditorFrame.setEditorIsHot(true);");
			if(($directory && $file) || $file){
				
				//javascript:we_cmd('browse_server','document.forms[0].elements[\\'$Path_elem_Name\\'].value','".(($directory && $file) ? "filefolder" : "")."',document.forms[0].elements['$Path_elem_Name'].value,'if (opener.opener != null){opener.opener._EditorFrame.setEditorIsHot(true);}else{opener._EditorFrame.setEditorIsHot(true);}".($showRadio ? "opener.document.we_form.elements[\'$int_elem_Name\'][1].checked=true;" : "")."')
				$wecmdenc1= we_cmd_enc("document.forms[0].elements['$Path_elem_Name'].value");
				$wecmdenc4= we_cmd_enc("if (opener.opener != null){opener.opener._EditorFrame.setEditorIsHot(true);}else{opener._EditorFrame.setEditorIsHot(true);}".($showRadio ? "opener.document.we_form.elements['$int_elem_Name'][1].checked=true;" : "")."");
				$but      = we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ?
					$we_button->create_button("select", "javascript:we_cmd('browse_server','".$wecmdenc1."','".(($directory && $file) ? "filefolder" : "")."',document.forms[0].elements['$Path_elem_Name'].value,'".$wecmdenc4."')"):
					"";
			}else{
				//javascript:formFileChooser('browse_server','document.we_form.elements[\\'$IDName\\'].value','$filter',document.we_form.elements['$IDName'].value,'$cmd');
				$wecmdenc1= we_cmd_enc("document.forms[0].elements['$Path_elem_Name'].value");
				$wecmdenc4= we_cmd_enc("if (opener.opener != null){opener.opener._EditorFrame.setEditorIsHot(true);}else{opener._EditorFrame.setEditorIsHot(true);}".($showRadio ? "opener.document.we_form.elements['$int_elem_Name'][1].checked=true;" : "")."");
				$but      = we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ?
					$we_button->create_button("select", "javascript:we_cmd('browse_server','".$wecmdenc1."','folder',document.forms[0].elements['$Path_elem_Name'].value,'".$wecmdenc4."')"):
					"";
			}

		}

		$out .='</td>
			<td>'.getPixel(6,4).'</td>
			<td>'.$but.'</td>
			<td>'.getPixel(5,2).'</td>
			<td>'.$trashbut.'</td>
		</tr>
';
		return $out;
	}

	/* creates a text-input field for entering Data that will be stored at the $elements Array */
	function formInput($name,$size=25,$type="txt"){
		global $l_we_class;
		return $this->formTextInput($type,$name,($l_we_class[$name] ? $l_we_class[$name] : $name),$size);
	}
	/* creates a color field. when user clicks, a colorchooser opens. Data that will be stored at the $elements Array */
	function formColor($width=100,$name,$size=25,$type="txt",$height=18,$isTag=false){
		global $l_we_class;
		$value = $this->getElement($name);
		if(!$isTag){
			$width -= 4;
		}
		$formname = "we_".$this->Name."_".$type."[$name]";
		$out = $this->htmlHidden($formname,$this->getElement($name)).'<table cellpadding="0" cellspacing="0" border="1"><tr><td'.($value ? (' bgcolor="'.$value.'"') : '').'><a href="javascript:setScrollTo();we_cmd(\'openColorChooser\',\''.$formname.'\',document.we_form.elements[\''.$formname.'\'].value);">'.getPixel($width,$height).'</a></td></tr></table>';
		return isset($l_we_class[$name]) ? $this->htmlFormElementTable($out,$l_we_class[$name]) : $out;
	}
	/* creates a select field for entering Data that will be stored at the $elements Array */
	function formSelectElement($name,$values,$type="txt",$size=1){
		global $l_we_class;
		$out = '<select class="defaultfont" name="we_'.$this->Name."_".$type."[$name]".'" size="'.$size.'">'."\n";
		$value = $this->getElement($name);
		reset($values);
		while(list($val,$txt) = each($values)){
			$out .= '<option value="'.$val.'"'.(($val==$value) ? " selected" : "").'>'.$txt."</option>\n";
		}
		$out .= "</select>\n";
		return $this->htmlFormElementTable($out,$l_we_class[$name]);
	}

	function formTextInput($elementtype,$name,$text,$size=24,$maxlength="",$attribs="",$textalign="left",$textclass="defaultfont"){
		global $l_we_class;
		if(!$elementtype) eval('$ps=$this->'.$name.";");
		return $this->htmlFormElementTable($this->htmlTextInput(($elementtype ? ("we_".$this->Name."_".$elementtype."[$name]") : ("we_".$this->Name."_".$name)),$size,($elementtype ? $this->getElement($name) : $ps),$maxlength,$attribs),$text,$textalign,$textclass);
	}
	function formInputField($elementtype,$name,$text,$size=24,$width,$maxlength="",$attribs="",$textalign="left",$textclass="defaultfont"){
		global $l_we_class;
		if(!$elementtype) eval('$ps=$this->'.$name.";");
		return $this->htmlFormElementTable($this->htmlTextInput(($elementtype ? ("we_".$this->Name."_".$elementtype."[$name]") : ("we_".$this->Name."_".$name)),$size, ($elementtype && $this->getElement($name) != "" ? $this->getElement($name) : (isset($GLOBALS["meta"][$name]) ? $GLOBALS["meta"][$name]["default"] : (isset($ps) ? $ps : "") )),$maxlength,$attribs,"text",$width),$text,$textalign,$textclass);
	}
	function formPasswordInput($elementtype,$name,$text,$size=24,$maxlength="",$attribs="",$textalign="left",$textclass="defaultfont"){
		global $l_we_class;
		return $this->htmlFormElementTable($this->htmlPasswordInput(($elementtype ? ("we_".$this->Name."_".$elementtype."[$name]") : ("we_".$this->Name."_".$name)),$size,"",$maxlength,$attribs),$text,$textalign,$textclass);
	}
	function formTextArea($elementtype,$name,$text,$rows=10,$cols=30,$attribs="",$textalign="left",$textclass="defaultfont"){
		global $l_we_class;
		if(!$elementtype) eval('$ps=$this->'.$name.";");
		return $this->htmlFormElementTable($this->htmlTextArea(($elementtype ? ("we_".$this->Name."_".$elementtype."[$name]") : ("we_".$this->Name."_".$name)),$rows,$cols,($elementtype ? $this->getElement($name) : $ps),$attribs),$text,$textalign,$textclass);
	}

	function formSelect($elementtype,$name,$table,$val,$txt,$text,$sqlTail="",$size=1,$selectedIndex="",$multiple=false,$attribs="",$textalign="left",$textclass="defaultfont",$precode="",$postcode="",$firstEntry=""){
		$vals = array();
		if($firstEntry) $vals[$firstEntry[0]] = $firstEntry[1];
		$this->DB_WE->query("SELECT * FROM $table $sqlTail");
		while($this->DB_WE->next_record()){
			$v = $this->DB_WE->f($val);
			$t = $this->DB_WE->f($txt);
			$vals[$v]=$t;
		}

		if(!$elementtype) eval('$ps=$this->'.$name.";");
		$pop = $this->htmlSelect(($elementtype ? ("we_".$this->Name."_".$elementtype."[$name]") : ("we_".$this->Name."_".$name)),$vals,$size,($elementtype ? $this->getElement($name) : $ps),$multiple,$attribs);
		return $this->htmlFormElementTable(($precode ? $precode : "").$pop.($postcode ? $postcode : ""),$text,$textalign,$textclass);

	}
	function formSelectFromArray($elementtype,$name,$vals,$text,$size=1,$selectedIndex="",$multiple=false,$attribs="",$textalign="left",$textclass="defaultfont",$precode="",$postcode="",$firstEntry=""){

		if(!$elementtype) eval('$ps=$this->'.$name.";");
		$pop = $this->htmlSelect2(($elementtype ? ("we_".$this->Name."_".$elementtype."[$name]") : ("we_".$this->Name."_".$name)),$vals,$size,($elementtype ? $this->getElement($name) : $ps),$multiple,$attribs);
		return $this->htmlFormElementTable(($precode ? $precode : "").$pop.($postcode ? $postcode : ""),$text,$textalign,$textclass);

	}

	function htmlTextInput($name,$size=24,$value="",$maxlength="",$attribs="",$type="text",$width="0",$height="0"){
		return htmlTextInput($name,$size,$value,$maxlength,$attribs,$type,$width,$height);
	}
	function htmlHidden($name,$value="", $params=null){
		return '<input type="hidden" name="'.trim($name).'" value="'.htmlspecialchars($value).'" '. $params .' />';
	}
	function htmlPasswordInput($name,$size=24,$value="",$maxlength="",$attribs=""){
		return $this->htmlTextInput($name,$size,$value,$maxlength,$attribs,"password");
	}
	function htmlTextArea($name,$rows=10,$cols=30,$value="",$attribs=""){
		return '<textarea class="defaultfont" name="'.trim($name).'" rows="'.abs($rows).'" cols="'.abs($cols).'"'.($attribs ? " $attribs" : '').'>'.($value ? (htmlspecialchars($value)) : '').'</textarea>';
	}
	function htmlRadioButton($name,$value,$checked=false,$attribs="",$text="",$textalign="left",$textclass="defaultfont",$type="radio",$width=""){
		$v=$value; //ereg_replace('"',"&quot;",$value);
		return (	$text ?
				('<table cellpadding="0" cellspacing="0" border="0"'.($width ? " width=$width" : "").'><tr>'.(($textalign=="left") ?
							('<td class="'.$textclass.'">'.$text.'&nbsp;</td><td>') :
							"<td>")
				) :
				""
			).'<input type="'.trim($type).'" name="'.trim($name).'" value="'.$v.'"'.($attribs ? " $attribs" : '').($checked ? " checked" : "").' />'.
			(	$text ?
				( (($textalign=="right") ?
					('</td><td class="'.$textclass.'">&nbsp;'.$text.'</td>') :
					'</td>'
				  ).'</tr></table>'
				) :
				""
			);

	}
	function htmlCheckBox($name,$value,$checked=false,$attribs="",$text="",$textalign="left",$textclass="defaultfont"){
		$v=$value; //ereg_replace('"',"&quot;",$value);
		$type = "checkbox";
		return (	$text ?
				('<table cellpadding="0" cellspacing="0" border="0"><tr>'.(($textalign=="left") ?
							('<td class="'.$textclass.'">'.$text.'&nbsp;</td><td>') :
							"<td>")
				) :
				""
			).'<input type="'.trim($type).'" name="'.trim($name).'" value="'.$v.'"'.($attribs ? " $attribs" : '').($checked ? " checked" : "").' />'.
			(	$text ?
				( (($textalign=="right") ?
					('</td><td class="'.$textclass.'">&nbsp;'.$text.'</td>') :
					'</td>'
				  ).'</tr></table>'
				) :
				""
			);

	}
	function htmlSelect($name,$values,$size=1,$selectedIndex="",$multiple=false,$attribs="",$compare="value",$width=""){
		if (is_array($values)) {
			reset($values);
		} else {
			$values = array();
		}
		$ret = '<select id="'.trim($name).'" class="weSelect defaultfont" name="'.trim($name).'" size="'.abs($size).'"'.($multiple ? " multiple" : "").($attribs ? " $attribs" : "").($width ? ' style="width: '.$width.'px"' : '').'>'."\n";
		$selIndex = split(",",$selectedIndex);
		while(list($value,$text) = each($values)){
			$ret .= '<option value="'.htmlspecialchars($value).'"'.(in_array((($compare == "value") ? $value : $text)."",$selIndex) ? " selected=\"selected\"" : "").'>'.$text."</option>\n";
		}
		$ret .= "</select>";
		return $ret;

	}

	// this function doesn't split selectedIndex
	function htmlSelect2($name,$values,$size=1,$selectedIndex="",$multiple=false,$attribs="",$compare="value",$width=""){
		reset($values);
		$ret = '<select id="'.trim($name).'" class="weSelect defaultfont" name="'.trim($name).'" size="'.abs($size).'"'.($multiple ? " multiple" : "").($attribs ? " $attribs" : "").($width ? ' style="width: '.$width.'px"' : '').'>'."\n";
		while(list($value,$text) = each($values)){
			$ret .= '<option value="'.htmlspecialchars($value).'"'.(($selectedIndex == (($compare == "value") ? $value : $text)) ?  " selected=\"selected\"" : "").'>'.$text."</option>\n";
		}
		$ret .= "</select>";
		return $ret;
	}


	function htmlFormElementTable($element,$text,$textalign="left",$textclass="defaultfont",$col2="",$col3="",$col4="",$col5="",$col6=""){
		return htmlFormElementTable($element,$text,$textalign,$textclass,$col2,$col3,$col4,$col5,$col6);
	}

	############## new fns
	/* creates a select field for entering Data that will be stored at the $elements Array */
	function formSelectElement2($width="",$name,$values,$type="txt",$size=1,$attribs=""){
		global $l_we_class;
		$out = '<select class="defaultfont" name="we_'.$this->Name."_".$type."[$name]".'" size="'.$size.'"'.($width ? ' style="width: '.$width.'px"' : '').($attribs ? " $attribs" : '').'>'."\n";
		$value = $this->getElement($name);
		reset($values);
		while(list($val,$txt) = each($values)){
			$out .= '<option value="'.$val.'"'.(($val==$value) ? " selected" : "").'>'.$txt."</option>\n";
		}
		$out .= "</select>\n";
		return $this->htmlFormElementTable($out,$l_we_class[$name]);
	}

	/* creates a text-input field for entering Data that will be stored at the $elements Array */
	function formInput2($width="",$name,$size=25,$type="txt",$attribs=""){
		global $l_we_class;
		return $this->formInputField($type,$name,(isset($l_we_class[$name]) ? $l_we_class[$name] : $name),$size,$width,"",$attribs);
	}

	/* creates a text-input field for entering Data that will be stored at the $elements Array and shows information from another Element*/
	function formInputInfo2($width="",$name,$size=25,$type="txt",$attribs="",$infoname){
		global $l_we_class;
		$info=$this->getElement($infoname);
		$infotext = " (".(isset($l_we_class[$infoname]) ? $l_we_class[$infoname] : $infoname) .": ".$info.")";
		return $this->formInputField($type,$name,(isset($l_we_class[$name]) ? $l_we_class[$name] : $name).$infotext,$size,$width,"",$attribs);
	}



	function formSelect2($elementtype,$width,$name,$table,$val,$txt,$text,$sqlTail="",$size=1,$selectedIndex="",$multiple=false,$onChange="",$attribs="",$textalign="left",$textclass="defaultfont",$precode="",$postcode="",$firstEntry="",$gap=20){
		$vals = array();
		if($firstEntry) $vals[$firstEntry[0]] = $firstEntry[1];
		$this->DB_WE->query("SELECT * FROM ".$this->DB_WE->escape($table)." $sqlTail");
		while($this->DB_WE->next_record()){
			$v = $this->DB_WE->f($val);
			$t = $this->DB_WE->f($txt);
			$vals[$v]=$t;
		}
		$myname = $elementtype ? ("we_".$this->Name."_".$elementtype."[$name]") : ("we_".$this->Name."_".$name);


		if($multiple){
			$onChange.= ";var we_sel='';for(i=0;i<this.options.length;i++){if(this.options[i].selected){we_sel += (this.options[i].value + ',');};};if(we_sel){we_sel=we_sel.substring(0,we_sel.length-1)};this.form.elements['".$myname."'].value=we_sel;";
			if(!$elementtype) eval('$ps=$this->'.$name.";");
			$pop = $this->htmlSelect($myname."Tmp",$vals,$size,($elementtype ? $this->getElement($name) : $ps),$multiple,"onChange=\"$onChange\" ".$attribs,"value",$width);

			if($precode || $postcode){
				$pop = '<table border="0" cellpadding="0" cellspacing="0"><tr>'.($precode ? ("<td>$precode</td><td>".getPixel($gap,2)."</td>") : "").'<td>'.$pop.'</td>'.($postcode ? ("<td>".getPixel($gap,2)."</td><td>$postcode</td>") : "").'</tr></table>';
			}

			return $this->htmlHidden($myname,$selectedIndex).$this->htmlFormElementTable($pop,$text,$textalign,$textclass);
		}else{
			if(!$elementtype) eval('$ps=$this->'.$name.";");
			$pop = $this->htmlSelect($myname,$vals,$size,($elementtype ? $this->getElement($name) : $ps),$multiple,"onChange=\"$onChange\" ".$attribs,"value",$width);
			if($precode || $postcode){
				$pop = '<table border="0" cellpadding="0" cellspacing="0"><tr>'.($precode ? ("<td>$precode</td><td>".getPixel($gap,2)."</td>") : "").'<td>'.$pop.'</td>'.($postcode ? ("<td>".getPixel($gap,2)."</td><td>$postcode</td>") : "").'</tr></table>';
			}
			return $this->htmlFormElementTable($pop,$text,$textalign,$textclass);
		}
	}

    function formSelect4($elementtype,$width,$name,$table,$val,$txt,$text,$sqlTail="",$size=1,$selectedIndex="",$multiple=false,$onChange="",$attribs="",$textalign="left",$textclass="defaultfont",$precode="",$postcode="",$firstEntry=""){
		$vals = array();
		if($firstEntry) $vals[$firstEntry[0]] = $firstEntry[1];
		$this->DB_WE->query("SELECT * FROM ".$this->DB_WE->escape($table)." $sqlTail");
		while($this->DB_WE->next_record()){
			$v = $this->DB_WE->f($val);
			$t = $this->DB_WE->f($txt);
			$vals[$v]=$t;
		}
		$myname = "we_".$this->Name."_".$name;


		if(!$elementtype) eval('$ps=$this->'.$name.";");
		$pop = $this->htmlSelect($myname,$vals,$size,$selectedIndex,$multiple,"onChange=\"$onChange\" ".$attribs,"value",$width);
		return $this->htmlFormElementTable(($precode ? $precode : "").$pop.($postcode ? $postcode : ""),$text,$textalign,$textclass);

	}




##### NEWSTUFF ####

# public ##################

	function initByID($ID,$Table="",$from=LOAD_MAID_DB){
	    if ($Table == "") {
	        $Table = FILE_TABLE;
	    }
		$this->ID=abs($ID);
		$this->Table=$Table;
		$this->we_load($from);
		$GLOBALS["we_ID"] = $ID;  // look if we need this !!
		$GLOBALS["we_Table"] = $Table;
		// init Customer Filter !!!!
		if ( isset($this->documentCustomerFilter) && defined( 'CUSTOMER_TABLE' ) ) {
			$this->initWeDocumentCustomerFilterFromDB();

		}
	}

	/**
	 * inits weDocumentCustomerFilter from db regarding the modelId
	 * is called from "we_textContentDocument::we_load"
	 * @see we_textContentDocument::we_load
	 */
	function initWeDocumentCustomerFilterFromDB() {
		$this->documentCustomerFilter = weDocumentCustomerFilter::getFilterOfDocument($this);

	}

	function we_new(){
		// overwrite
	}

	function we_load($from=LOAD_MAID_DB){
		$this->i_getPersistentSlotsFromDB();

	}

	function we_save($resave=0){
		$this->wasUpdate= $this->ID ? 1 : 0;
		return $this->i_savePersistentSlotsToDB();
	}

	function we_initSessDat($sessDat){
		// overwrite
	}

	function we_publish($DoNotMark=false,$saveinMainDB=true){
		return true; // overwrite
	}

	function we_unpublish($DoNotMark=false){
		return true; // overwrite
	}

	function we_republish(){
		return true;
	}

	function we_delete(){
		if (defined('LANGLINK_SUPPORT') && LANGLINK_SUPPORT ){
			$deltype='';
			if ($this->ClassName=='we_objectFile') $deltype='tblObjectFile';
			if ($this->ClassName=='we_webEditionDocument') $deltype='tblFile';
			if ($this->ClassName=='we_docTypes') $deltype='tblDocTypes';
			$this->DB_WE->query("DELETE FROM ".LANGLINK_TABLE." WHERE DocumentTable='".$deltype."' AND DID='".abs($this->ID)."'");
			$this->DB_WE->query("DELETE FROM ".LANGLINK_TABLE." WHERE DocumentTable='".$deltype."' AND LDID='".abs($this->ID)."'");
		}
		return $this->DB_WE->query("DELETE FROM ".$this->DB_WE->escape($this->Table)." WHERE ID='".abs($this->ID)."'");	
		
	}

# private ###################


	function i_setElementsFromHTTP(){

		// do not set REQUEST VARS into the document
		if(		($_REQUEST['we_cmd'][0] == "switch_edit_page" && isset($_REQUEST['we_cmd'][3]))
			||	($_REQUEST['we_cmd'][0] == "save_document" && isset($_REQUEST['we_cmd'][7]) && $_REQUEST['we_cmd'][7] == "save_document")) {
			return true;
		}
		if(sizeof($_REQUEST)){
			foreach($_REQUEST as $n=>$v){
				if(ereg('^we_'.$this->Name.'_([^\[]+)$',$n,$regs)){
					if(in_array($regs[1],$this->persistent_slots)){
				 		eval('$this->'.$regs[1].'=$v;');
					}
				}
			}
		}
	}

	function i_getPersistentSlotsFromDB($felder="*"){
		$this->DB_WE->query("SELECT ".$felder." FROM ".$this->DB_WE->escape($this->Table)." WHERE ID='".abs($this->ID)."'");
		if($this->DB_WE->next_record()){
			foreach($this->DB_WE->Record as $k=>$v){
				if($k && in_array($k,$this->persistent_slots)){
					eval('$this->'.$k.'=$v;');
				}
			}
		} else {
			$this->fileExists = 0;
		}
	}

	function i_fixCSVPrePost($in){
		if($in){
			if(substr($in,0,1) != ","){
				$in = ",".$in;
			}
			if(substr($in,-1) != ","){
				$in .= ",";
			}
		}
		return $in;
	}

	function i_savePersistentSlotsToDB($felder=""){
		$tableInfo = $this->DB_WE->metadata($this->Table);
		$feldArr = $felder ? makeArrayFromCSV($felder) : $this->persistent_slots;
		if($this->wasUpdate){
			$updt = "";
			for($i=0;$i<sizeof($tableInfo);$i++){
				$fieldName = $tableInfo[$i]["name"];
				if(in_array($fieldName,$feldArr)){
					eval('if(isset($this->'.$fieldName.')) $val = $this->'.$fieldName.';');
					if($fieldName == "Category"){ // Category-Fix!
						$val = $this->i_fixCSVPrePost($val);
					}
					if($fieldName != "ID") $updt .= $fieldName."='".addslashes($val)."',";
				}
			}
			$updt = substr($updt,0,-1);
			if($updt){
				$q = "UPDATE ".$this->DB_WE->escape($this->Table)." SET ".$updt." WHERE ID='".abs($this->ID)."'";
				return ($this->DB_WE->query($q)?true:false);
			}else{
				return false;
			}
		}else{
			$keys = "";
			$vals = "";
			for($i=0;$i<sizeof($tableInfo);$i++){
				$fieldName = $tableInfo[$i]["name"];
				if(in_array($fieldName,$feldArr)){
					eval('$val = $this->'.$fieldName.';');
					if($fieldName == "Category"){ // Category-Fix!
						$val = $this->i_fixCSVPrePost($val);
					}
					if($fieldName != "ID"){
						$keys .= $fieldName.",";
						$vals .= "'".addslashes($val)."',";
					}
				}
			}
			if($keys){
				$keys = "(".substr($keys,0,strlen($keys)-1).")";
				$vals = "VALUES(".substr($vals,0,strlen($vals)-1).")";
				$q = "INSERT INTO ".$this->DB_WE->escape($this->Table)." $keys $vals";
				if($this->DB_WE->query($q)){
    				$this->ID = f("SELECT MAX(LAST_INSERT_ID()) as LastID FROM ".$this->DB_WE->escape($this->Table),"LastID",$this->DB_WE);
					return true;
				}
				return false;
			}
		}

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

	function isValidEditPage($editPageNr) {

		if (is_array($this->EditPageNrs)) {
			return in_array($editPageNr, $this->EditPageNrs);

		}
		return false;

	}
	
	function setLanguageLink($LangLinkArray,$type,$isfolder=false,$isobject=false){
		$db = new DB_WE;
		if(is_array($LangLinkArray) ){
			$q="SELECT * FROM ".LANGLINK_TABLE." WHERE  DID='".abs($this->ID)."'";
			$orig=array();
			$this->DB_WE->query($q);
			while($this->DB_WE->next_record()){
				$orig[]=$this->DB_WE->Record;
			}
			$max= count($orig);			
			for($j=0;$j<$max;$j++){
				$q="SELECT * FROM ".LANGLINK_TABLE." WHERE  DID='".abs($orig[$j]['LDID'])."'";
				$this->DB_WE->query($q);
				while($this->DB_WE->next_record()){
					$orig[]=$this->DB_WE->Record;
				}
			}
			foreach ($LangLinkArray as $locale => $LDID){
				if($ID = f("SELECT ID FROM ".LANGLINK_TABLE." WHERE DocumentTable='".$type."' AND DID='".abs($this->ID)."' AND Locale='".$locale."' AND IsObject='".abs($isobject)."'",'ID',$this->DB_WE)){
					if ($LDID>0){
						$q = "UPDATE ".LANGLINK_TABLE." SET LDID='".abs($LDID)."',DLocale='".$this->Language."' WHERE ID='".abs($ID)."'";
					} else {
						$q = "UPDATE ".LANGLINK_TABLE." SET LDID='0',DLocale='".$this->Language."' WHERE ID='".abs($ID)."'";
					}
					$this->DB_WE->query($q);
				} else {
					if($locale!=$this->Language){
						if ($LDID>0){
							$q = "INSERT INTO ".LANGLINK_TABLE." SET DID='".abs($this->ID)."',DLocale='".$this->Language."',IsFolder='".abs($isfolder)."', IsObject='".abs($isobject)."', LDID='".abs($LDID)."', Locale='".$locale."', DocumentTable='".$type."';";
							$this->DB_WE->query($q);
						}
					}
				}
				if(( (defined('LANGLINK_SUPPORT_BACKLINKS') && LANGLINK_SUPPORT_BACKLINKS) || (defined('LANGLINK_SUPPORT_RECURSIVE') && LANGLINK_SUPPORT_RECURSIVE) ) && !$isfolder && $LDID && $LDID!=$this->ID){
					$q='';
					if($ID = f("SELECT ID FROM ".LANGLINK_TABLE." WHERE DocumentTable='".$type."' AND DID='".abs($LDID)."' AND Locale='".$this->Language."' AND IsObject='".abs($isobject)."'",'ID',$this->DB_WE)){
						if ($LDID>0){
							$q = "UPDATE ".LANGLINK_TABLE." SET DID='".abs($LDID)."', DLocale='".$locale."', LDID='".abs($this->ID)."',Locale='".$this->Language."' WHERE ID='".abs($ID)."'";
						}
						if ($LDID<0){
							$q = "UPDATE ".LANGLINK_TABLE." SET DID='".abs($LDID)."', DLocale='".$locale."', LDID='0',Locale='".$this->Language."' WHERE ID='".abs($ID)."'";
						}  
					} else {
						if ($LDID>0){
							$q = "INSERT INTO ".LANGLINK_TABLE." SET DID='".abs($LDID)."', DLocale='".$locale."', LDID='".abs($this->ID)."', Locale='".$this->Language."', IsObject='".abs($isobject)."', DocumentTable='".$type."';";
						}
					}
					if($q){$this->DB_WE->query($q);}
				}
				if(( (defined('LANGLINK_SUPPORT_BACKLINKS') && LANGLINK_SUPPORT_BACKLINKS) || (defined('LANGLINK_SUPPORT_RECURSIVE') && LANGLINK_SUPPORT_RECURSIVE) ) && !$isfolder && $LDID<0&& $LDID!=$this->ID){
					$q='';
					if($ID = f("SELECT ID FROM ".LANGLINK_TABLE." WHERE DocumentTable='".$type."' AND DID='".abs($LDID)."' AND Locale='".$this->Language."' AND IsObject='".abs($isobject)."'",'ID',$this->DB_WE)){
						if ($LDID>0){
							$q = "UPDATE ".LANGLINK_TABLE." SET DID='".abs($LDID)."', DLocale='".$locale."', LDID='".abs($this->ID)."',Locale='".$this->Language."' WHERE ID='".abs($ID)."'";
						} 
					} else {
						if ($LDID>0){
							$q = "INSERT INTO ".LANGLINK_TABLE." SET DID='".abs($LDID)."', DLocale='".$locale."', LDID='".abs($this->ID)."', Locale='".$this->Language."', IsObject='".abs($isobject)."', DocumentTable='".$type."';";
						}
					}
					if($q){$this->DB_WE->query($q);}
				}
		  }	
			if(defined('LANGLINK_SUPPORT_RECURSIVE') && LANGLINK_SUPPORT_RECURSIVE && !$isfolder){		
				foreach ($LangLinkArray as $locale => $LDID){
					if($LDID>0){
						$rows=array();
						$q= "SELECT * FROM ".LANGLINK_TABLE." WHERE  DID='".abs($this->ID)."'  AND DocumentTable='".$type."' AND IsObject='".abs($isobject)."'";
						$this->DB_WE->query($q);
						while($this->DB_WE->next_record()){
							$rows[]=$this->DB_WE->Record;
						}
						if(count($rows)>1){
							for($i=0;$i<count($rows)-1;$i++){
								if($rows[$i]['LDID']  && $rows[$i+1]['LDID']){
									if($ID = f("SELECT ID FROM ".LANGLINK_TABLE." WHERE DID='".abs($rows[$i]['LDID'])."' AND DLocale='".$rows[$i]['Locale']."' AND LDID='".abs($rows[$i+1]['LDID'])."' AND Locale='".$rows[$i+1]['Locale']."' AND DocumentTable='".$type."' AND IsObject='".abs($isobject)."'",'ID',$this->DB_WE)){
										$q = "UPDATE ".LANGLINK_TABLE." SET DID='".abs($rows[$i]['LDID'])."', DLocale='".$rows[$i]['Locale']."', LDID='".abs($rows[$i+1]['LDID'])."',Locale='".$rows[$i+1]['Locale']."' WHERE ID='".abs($ID)."'";
									} else {
										$q = "INSERT INTO ".LANGLINK_TABLE." SET DID='".abs($rows[$i]['LDID'])."', DLocale='".$rows[$i]['Locale']."', LDID='".abs($rows[$i+1]['LDID'])."', Locale='".$rows[$i+1]['Locale']."', IsObject='".abs($isobject)."', DocumentTable='".$type."';";					
									}
									$this->DB_WE->query($q);
									if($rows[$i+1]['LDID'] && $ID = f("SELECT ID FROM ".LANGLINK_TABLE." WHERE DID='".abs($rows[$i+1]['LDID'])."' AND DLocale='".$rows[$i+1]['Locale']."' AND LDID='".abs($rows[$i]['LDID'])."' AND Locale='".$rows[$i]['Locale']."' AND DocumentTable='".$type."' AND IsObject='".abs($isobject)."'",'ID',$this->DB_WE)){
										$q = "UPDATE ".LANGLINK_TABLE." SET DID='".abs($rows[$i+1]['LDID'])."', DLocale='".$rows[$i+1]['Locale']."', LDID='".abs($rows[$i]['LDID'])."',Locale='".$rows[$i]['Locale']."' WHERE ID='".abs($ID)."'";
									} else {
										$q = "INSERT INTO ".LANGLINK_TABLE." SET DID='".abs($rows[$i+1]['LDID'])."', DLocale='".$rows[$i+1]['Locale']."', LDID='".abs($rows[$i]['LDID'])."', Locale='".$rows[$i]['Locale']."', IsObject='".abs($isobject)."', DocumentTable='".$type."';";					
									}
									$this->DB_WE->query($q);
								}
							}			
						}
					}
				if ($LDID<0){
					foreach($orig as $origrow){
						if($origrow['DLocale']==$locale){
							$q="SELECT ID FROM ".LANGLINK_TABLE." WHERE  DID='".abs($origrow['DID'])."' AND DLocale='".$locale."' AND DocumentTable='".$type."' AND IsObject='".abs($isobject)."'";
							$this->DB_WE->query($q);
							while($this->DB_WE->next_record()){
								$delRowID = $this->DB_WE->Record['ID'];
								$qd= "UPDATE ".LANGLINK_TABLE." SET LDID='0' WHERE ID='".abs($delRowID)."'";
								$db->query($qd);
							}
						}
						if($origrow['Locale']==$locale){
							$q="SELECT ID FROM ".LANGLINK_TABLE." WHERE  LDID='".abs($origrow['LDID'])."' AND Locale='".$locale."' AND DocumentTable='".$type."' AND IsObject='".abs($isobject)."'";
							$this->DB_WE->query($q);
							while($this->DB_WE->next_record()){
								$delRowID = $this->DB_WE->Record['ID'];
								$qd= "UPDATE ".LANGLINK_TABLE." SET LDID='0' WHERE ID='".abs($delRowID)."'";
								$db->query($qd);
							}
						}
					}
				}
						
						
				}
			  
			}
		}
	}

	/**returns error-messages recorded during an operation, currently only save is used*/
	function getErrMsg(){
		return ($this->errMsg !='' ?'\n'.$this->errMsg:'');
	}
	
}
