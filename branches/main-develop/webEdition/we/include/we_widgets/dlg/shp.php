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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
include_once (WE_INCLUDES_PATH . 'we_widgets/dlg/prefs.inc.php');
we_html_tools::protect();

list($sType, $iDate, $sRevenueTarget) = explode(";", $_REQUEST['we_cmd'][1]);

$jsCode = "
var _oCsv_;
var _sInitCsv_;
var _sShpInc='shp/shp';
var _oSctDate;
var _sInitNum='" . $sRevenueTarget . "';
var _bPrev=false;
var _sLastPreviewCsv='';

function init(){
	_fo=document.forms[0];
	_oCsv_=opener.gel(_sObjId+'_csv')
	_sInitCsv_=_oCsv_.value;
	_oSctDate=_fo.elements['sct_date'];
	_fo.elements['revenueTarget'].value=_sInitNum;
	initPrefs();
	//alert('form: ' + _fo.name);
}

function getBinary(postfix){
	var sBinary='';
	var oChbx=_fo.elements['chbx_'+postfix];
	var iChbxLen=oChbx.length;
	for(var i=0;i<iChbxLen;i++){
		sBinary+=(oChbx[i].checked)?'1':'0';
	}
	return sBinary;
}


function getCsv(){
	return getBinary('type')+';'+_oSctDate.selectedIndex+';'+_fo.elements['revenueTarget'].value;
}

function refresh(bRender){
	if(bRender)_sLastPreviewCsv=getCsv();
	opener.rpc(getBinary('type'),_oSctDate.selectedIndex,_sObjId,_sShpInc);
}

function save(){
	if(isNoError()) {
		var sCsv=getCsv();
		_oCsv_.value=sCsv;
		savePrefs();
		opener.saveSettings();
		if((!_bPrev&&sCsv!=_sInitCsv_)||(_bPrev&&sCsv!=_sLastPreviewCsv)){
			refresh(false);
		}
		" . we_message_reporting::getShowMessageCall(
		g_l('cockpit', '[prefs_saved_successfully]'), we_message_reporting::WE_MESSAGE_NOTICE) . "
		self.close();
	} else {
		" . we_message_reporting::getShowMessageCall(
		g_l('cockpit', '[no_type_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . "
	}
}

function isNoError(){
	chbx_type_checked = false;
	for( var chbx_type_i = 0; chbx_type_i < document.we_form.chbx_type.length; chbx_type_i++) {
		if(document.we_form.chbx_type[chbx_type_i].checked) chbx_type_checked = true;
	}
	return chbx_type_checked;
}
function preview(){
	if(isNoError()) {
		_bPrev=true;
		previewPrefs();
		refresh(true);
	} else {
		" . we_message_reporting::getShowMessageCall(
		g_l('cockpit', '[no_type_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . "
	}
}

function exit_close(){
	if(_bPrev&&_sInitCsv_!=_sLastPreviewCsv){
		var aCsv=_sInitCsv_.split(';');
		opener.rpc(aCsv[0],aCsv[1],aCsv[2],aCsv[3],aCsv[4],_sObjId,_sShpInc);
	}
	exitPrefs();
	self.close();
}

";

// Typ block
while(strlen($sType) < 4) {
	$sType .= "0";
}
if($sType{0} == "0" && $sType{1} == "0" && $sType{2} == "0" && $sType{3} == "0"){
	$sType = "1111";
}

if(defined("CUSTOMER_TABLE") && permissionhandler::hasPerm("CAN_SEE_CUSTOMER")){
	$oChbxCustomer = we_html_forms::checkbox(
			$value = 0, $checked = $sType{1}, $name = "chbx_type", $text = g_l('cockpit','[shop_dashboard][cnt_new_customer]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = !(defined("CUSTOMER_TABLE") && permissionhandler::hasPerm('CAN_SEE_CUSTOMER')), $description = "", $type = 0, $width = 0);
} else{
	$oChbxCustomer = "";
}

if(defined("WE_SHOP_MODULE_DIR") && permissionhandler::hasPerm("CAN_SEE_SHOP")){
	$oChbxOrders = we_html_forms::checkbox(
			$value = 0, $checked = $sType{0}, $name = "chbx_type", $text = g_l('cockpit','[shop_dashboard][cnt_order]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = !(defined('WE_SHOP_MODULE_DIR') && permissionhandler::hasPerm("CAN_SEE_SHOP")), $description = "", $type = 0, $width = 0);
	$oChbxAverageOrder = we_html_forms::checkbox(
			$value = 0, $checked = $sType{2}, $name = "chbx_type", $text = g_l('cockpit','[shop_dashboard][revenue_order]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = !(defined("WE_SHOP_MODULE_DIR") && permissionhandler::hasPerm('CAN_SEE_SHOP')), $description = "", $type = 0, $width = 0);
	$oChbxTarget = we_html_forms::checkbox(
			$value = 0, $checked = $sType{3}, $name = "chbx_type", $text = g_l('cockpit','[shop_dashboard][revenue_target]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = !(defined("WE_SHOP_MODULE_DIR") && permissionhandler::hasPerm('CAN_SEE_SHOP')), $description = "", $type = 0, $width = 0);
			
	//$revenueTarget = we_forms::textinput($value = "",$name = "input_revenueTarget", $text = "Umsatzziel", $uniqid = true, $class = "defaultfont",$onClick = "", $disabled = !(defined("WE_SHOP_MODULE_DIR") && permissionhandler::hasPerm('CAN_SEE_SHOP'), $description = "", $type = 0, $width = 255);
} else{
	$oChbxOrders = "";
	$oChbxAverageOrder = "";
	$oChbxTarget = "";
}

$oDbTableType = new we_html_table(array(
	"border" => 0, "cellpadding" => 0, "cellspacing" => 0
	), 1, 3);
$oDbTableType->setCol(0, 0, null, $oChbxOrders . $oChbxCustomer);
$oDbTableType->setCol(0, 1, null, we_html_tools::getPixel(10, 1));
$oDbTableType->setCol(0, 2, null, $oChbxAverageOrder . $oChbxTarget);
//$oDbTableType->setCol(0, 3, null, $revenueTarget);

$divContent = we_html_element::htmlDiv(array("style" => "display:block;"),we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($name = "revenueTarget", $size = 55, $value = $sRevenueTarget, $maxlength = 255, $attribs = "", $type = "text", $width = 100, $height = 0)."&nbsp;&euro;", '', "left", "defaultfont"));

$oSctDate = new we_html_select(array(
	"name" => "sct_date", "size" => 1, "class" => "defaultfont", "onChange" => ""
	));
$aLangDate = array(
	g_l('cockpit', '[today]'),
	g_l('cockpit', '[this_week]'),
	g_l('cockpit', '[last_week]'),
	g_l('cockpit', '[this_month]'),
	g_l('cockpit', '[last_month]'),
	g_l('cockpit', '[this_year]'),
	g_l('cockpit', '[last_year]')
);
foreach($aLangDate as $k => $v){
	$oSctDate->insertOption($k, $k, $v);
}
$oSctDate->selectOption($iDate);

$parts = array(
	array(
		"headline" => g_l('cockpit', '[shop_dashboard][kpi]'), "html" => $oDbTableType->getHTML(), "space" => 80
	),
	array(
		"headline" => g_l('cockpit', '[shop_dashboard][revenue_target]'), "html" => $divContent, "space" => 80
	),
	array(
		"headline" => g_l('cockpit', '[date]'), "html" => $oSctDate->getHTML(), "space" => 80
	),
	array(
		"headline" => g_l('cockpit', '[display]'), "html" => $oSelCls->getHTML(), "space" => 0
	)
);

$save_button = we_button::create_button("save", "javascript:save();", false, -1, -1);
$preview_button = we_button::create_button("preview", "javascript:preview();", false, -1, -1);
$cancel_button = we_button::create_button("close", "javascript:exit_close();");
$buttons = we_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

$sTblWidget = we_html_multiIconBox::getHTML(
		"shpProps", "100%", $parts, 30, $buttons, -1, "", "", "", "Shop", "", 390);

print we_html_element::htmlDocType() . we_html_element::htmlHtml(
		we_html_element::htmlHead(
			we_html_tools::getHtmlInnerHead(g_l('cockpit', '[shop_dashboard][headline]')) . STYLESHEET . we_html_element::cssElement(
				"select{border:#AAAAAA solid 1px}") . we_html_element::jsScript(JS_DIR . "we_showMessage.js") .
			we_html_element::jsElement(
				$jsPrefs . $jsCode . we_button::create_state_changer(false))) . we_html_element::htmlBody(
			array(
			"class" => "weDialogBody", "onload" => "init();"
			), we_html_element::htmlForm("", $sTblWidget)));