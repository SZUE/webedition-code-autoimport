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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
include_once (WE_INCLUDES_PATH . 'we_widgets/dlg/prefs.inc.php');
we_html_tools::protect();

list($sType, $iDate, $sRevenueTarget) = explode(";", we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1));

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
	_oSctDate=_fo.elements.sct_date;
	_fo.elements.revenueTarget.value=_sInitNum;
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
	return getBinary('type')+';'+_oSctDate.selectedIndex+';'+_fo.elements.revenueTarget.value;
}

function refresh(bRender){
	if(bRender)_sLastPreviewCsv=getCsv();
	opener.rpc(getBinary('type'),_oSctDate.selectedIndex,document.forms[0].elements.revenueTarget.value,'','',_sObjId,_sShpInc);
}

function save(){
	if(isNoError()) {
		var sCsv=getCsv();
		_oCsv_.value=sCsv;
		//savePrefs();
		opener.saveSettings();
		if((!_bPrev&&sCsv!=_sInitCsv_)||(_bPrev&&sCsv!=_sLastPreviewCsv)){
			refresh(false);
		}
		top.we_showMessage(WE().consts.g_l.main.prefs_saved_successfully, WE_MESSAGE_NOTICE, window);
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
}";

// Typ block
while(strlen($sType) < 4){
	$sType .= '0';
}
if($sType === "0000"){
	$sType = "1111";
}

$oChbxCustomer = (defined('CUSTOMER_TABLE') && permissionhandler::hasPerm("CAN_SEE_CUSTOMER") ?
		we_html_forms::checkbox(0, $sType{1}, "chbx_type", g_l('cockpit', '[shop_dashboard][cnt_new_customer]'), true, "defaultfont", "", !(defined('CUSTOMER_TABLE') && permissionhandler::hasPerm('CAN_SEE_CUSTOMER')), "", 0, 0) :
		'');

if(defined('WE_SHOP_MODULE_DIR') && (permissionhandler::hasPerm("NEW_SHOP_ARTICLE") || permissionhandler::hasPerm("DELETE_SHOP_ARTICLE") || permissionhandler::hasPerm("EDIT_SHOP_ORDER") || permissionhandler::hasPerm("DELETE_SHOP_ORDER") || permissionhandler::hasPerm("EDIT_SHOP_PREFS"))){
	$oChbxOrders = we_html_forms::checkbox(0, $sType{0}, "chbx_type", g_l('cockpit', '[shop_dashboard][cnt_order]'), true, "defaultfont", "", !(defined('WE_SHOP_MODULE_DIR') && permissionhandler::hasPerm("CAN_SEE_SHOP")), "", 0, 0);
	$oChbxAverageOrder = we_html_forms::checkbox(0, $sType{2}, "chbx_type", g_l('cockpit', '[shop_dashboard][revenue_order]'), true, "defaultfont", "", !(defined('WE_SHOP_MODULE_DIR') && permissionhandler::hasPerm('CAN_SEE_SHOP')), "", 0, 0);
	$oChbxTarget = we_html_forms::checkbox(0, $sType{3}, "chbx_type", g_l('cockpit', '[shop_dashboard][revenue_target]'), true, "defaultfont", "", !(defined('WE_SHOP_MODULE_DIR') && permissionhandler::hasPerm('CAN_SEE_SHOP')), "", 0, 0);

	//$revenueTarget = we_html_forms::textinput($value = "",$name = "input_revenueTarget", $text = "Umsatzziel", $uniqid = true, $class = "defaultfont",$onClick = "", $disabled = !(defined('WE_SHOP_MODULE_DIR') && permissionhandler::hasPerm('CAN_SEE_SHOP'), $description = "", $type = 0, $width = 255);
} else {
	$oChbxOrders = $oChbxAverageOrder = $oChbxTarget = "";
}

$oDbTableType = new we_html_table(array('class' => 'default'), 1, 3);
$oDbTableType->setCol(0, 0, null, $oChbxOrders . $oChbxCustomer);
$oDbTableType->setCol(0, 1, array('width' => 10));
$oDbTableType->setCol(0, 2, null, $oChbxAverageOrder . $oChbxTarget);
//$oDbTableType->setCol(0, 3, null, $revenueTarget);

$oSctDate = new we_html_select(array(
	"name" => "sct_date", "size" => 1, "class" => "defaultfont", "onchange" => ""
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
		"headline" => g_l('cockpit', '[shop_dashboard][revenue_target]'),
		"html" => we_html_element::htmlDiv(array("style" => "display:block;"), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($name = "revenueTarget", $size = 55, $value = $sRevenueTarget, $maxlength = 255, $attribs = "", $type = "text", $width = 100, $height = 0) . "&nbsp;&euro;", '', "left", "defaultfont"))
		, "space" => 80
	),
	array(
		"headline" => g_l('cockpit', '[date]'), "html" => $oSctDate->getHTML(), "space" => 80
	),
	array(
		"headline" => g_l('cockpit', '[display]'), "html" => $oSelCls->getHTML(), "space" => 0
	)
);

$save_button = we_html_button::create_button(we_html_button::SAVE, "javascript:save();", false, 0, 0);
$preview_button = we_html_button::create_button(we_html_button::PREVIEW, "javascript:preview();", false, 0, 0);
$cancel_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:exit_close();");
$buttons = we_html_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

$sTblWidget = we_html_multiIconBox::getHTML("shpProps", $parts, 30, $buttons, -1, "", "", "", "Shop", "", 390);

echo we_html_tools::getHtmlTop(g_l('cockpit', '[shop_dashboard][headline]'), '', '', STYLESHEET .
	we_html_element::jsElement($jsPrefs . $jsCode), we_html_element::htmlBody(
		array(
		"class" => "weDialogBody", "onload" => "init();"
		), we_html_element::htmlForm("", $sTblWidget)));
