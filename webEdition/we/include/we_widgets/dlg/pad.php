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
$jsCode = "
var _oCsv_;
var _sInitCsv_;
var _sInitTitle;
var _sInitBin;
var _sPadInc='pad/pad';
var _oSctDate;
var _aRdo=['sort','display','date','prio'];
var _lastPreviewCsv='';

function init(){
	_fo=document.forms[0];
	_oCsv_=opener.gel(_sObjId+'_csv');
	_sInitCsv_=_oCsv_.value;
	var aCsv=_sInitCsv_.split(',');
	_sInitTitle=opener.base64_decode(aCsv[0]);
	_sInitBin=aCsv[1];
	for(var i=0;i<_aRdo.length;i++){
		_fo.elements['rdo_'+_aRdo[i]][_sInitBin.charAt(i)].checked=true;
	}
	_fo.elements['sct_valid'].options[_sInitBin.charAt(4)].selected=true;
	var oSctTitle=_fo.elements['sct_title'];
	for(var i=oSctTitle.length-1;i>=0;i--){
		oSctTitle.options[i].selected=(oSctTitle.options[i].text==_sInitTitle)?true:false;
	}
	initPrefs();
}

function getRdoChecked(sType){
	var oRdo=_fo.elements['rdo_'+sType];
	var iRdoLen=oRdo.length;
	for(var i=0;iRdoLen>i;i++){
		if(oRdo[i].checked==true) return i;
	}
}

function getBitString(){
	var sBit='';
	for(var i=0;i<_aRdo.length;i++){
		var iCurr=getRdoChecked(_aRdo[i]);
		sBit+=(typeof iCurr!='undefined')?iCurr:'0';
	}
	sBit+=_fo.elements['sct_valid'].selectedIndex;
	return sBit;
}

function getTitle(){
	var oSctTitle=_fo.elements['sct_title'];
	return oSctTitle[oSctTitle.selectedIndex].value;
}

function save(){
	var oCsv_=opener.gel(_sObjId+'_csv');
	var sTitleEnc=opener.base64_encode(getTitle());
	var sBit=getBitString();
	oCsv_.value=sTitleEnc.concat(','+sBit);
	if((_lastPreviewCsv!=''&&sTitleEnc.concat(','+sBit)!=_lastPreviewCsv)||
		(_lastPreviewCsv==''&&(_sInitTitle!=getTitle()||_sInitBin!=getBitString()))){
		var sTitleEsc=escape(sTitleEnc);
		opener.rpc(sTitleEsc.concat(','+sBit),'','','',sTitleEsc,_sObjId,_sPadInc);
	}
	opener.setPrefs(_sObjId,sBit,sTitleEnc);
	opener.saveSettings();
	savePrefs();
	" . we_message_reporting::getShowMessageCall(
		g_l('cockpit', '[prefs_saved_successfully]'), we_message_reporting::WE_MESSAGE_NOTICE) . "
	opener.top.weNavigationHistory.navigateReload();
	self.close();
}

function preview(){
	var sTitleEnc=opener.base64_encode(getTitle());
	var sTitleEsc=escape(sTitleEnc);
	var sBit=getBitString();
	opener.rpc(sTitleEsc.concat(','+sBit),'','','',sTitleEsc,_sObjId,_sPadInc);
	previewPrefs();
	_lastPreviewCsv=sTitleEnc.concat(','+sBit);
}

function exit_close(){
	if(_lastPreviewCsv!=''&&(_sInitTitle!=getTitle()||_sInitBin!=getBitString())){
		opener.rpc(_sInitCsv_,'','','',escape(opener.base64_encode(_sInitTitle)),_sObjId,_sPadInc);
	}
	exitPrefs();
	self.close();
}
";

$oRdoSort[0] = we_forms::radiobutton(
		$value = 0, $checked = 0, $name = "rdo_sort", $text = g_l('cockpit', '[by_pubdate]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");
$oRdoSort[1] = we_forms::radiobutton(
		$value = 1, $checked = 0, $name = "rdo_sort", $text = g_l('cockpit', '[by_valid_from]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");
$oRdoSort[2] = we_forms::radiobutton(
		$value = 2, $checked = 0, $name = "rdo_sort", $text = g_l('cockpit', '[by_valid_until]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");
$oRdoSort[3] = we_forms::radiobutton(
		$value = 3, $checked = 0, $name = "rdo_sort", $text = g_l('cockpit', '[by_priority]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");
$oRdoSort[4] = we_forms::radiobutton(
		$value = 4, $checked = 1, $name = "rdo_sort", $text = g_l('cockpit', '[alphabetic]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");

$sort = new we_html_table(array(
		"cellpadding" => "0", "cellspacing" => "0", "border" => "0"
		), 3, 3);
$sort->setCol(0, 0, array(
	"width" => 145
	), $oRdoSort[0]);
$sort->setCol(0, 1, null, we_html_tools::getPixel(10, 1));
$sort->setCol(0, 2, array(
	"width" => 145
	), $oRdoSort[3]);
$sort->setCol(1, 0, null, $oRdoSort[1]);
$sort->setCol(1, 2, null, $oRdoSort[4]);
$sort->setCol(2, 0, null, $oRdoSort[2]);

$parts = array(
	array(
		"headline" => g_l('cockpit', '[sorting]'), "html" => $sort->getHTML(), "space" => 100
	)
);

$oRdoDisplay[0] = we_forms::radiobutton(
		$value = 0, $checked = 1, $name = "rdo_display", $text = g_l('cockpit', '[all_notes]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");
$oRdoDisplay[1] = we_forms::radiobutton(
		$value = 1, $checked = 0, $name = "rdo_display", $text = g_l('cockpit', '[only_valid]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");

$display = new we_html_table(array(
		"cellpadding" => "0", "cellspacing" => "0", "border" => "0"
		), 1, 3);
$display->setCol(0, 0, array(
	"width" => 145
	), $oRdoDisplay[0]);
$display->setCol(0, 1, null, we_html_tools::getPixel(10, 1));
$display->setCol(0, 2, array(
	"width" => 145
	), $oRdoDisplay[1]);

$parts[] = array(
	"headline" => g_l('cockpit', '[display]'), "html" => $display->getHTML(), "space" => 100
);

$oRdoDate[0] = we_forms::radiobutton(
		$value = 0, $checked = 1, $name = "rdo_date", $text = g_l('cockpit', '[by_pubdate]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");
$oRdoDate[1] = we_forms::radiobutton(
		$value = 1, $checked = 0, $name = "rdo_date", $text = g_l('cockpit', '[by_valid_from]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");
$oRdoDate[2] = we_forms::radiobutton(
		$value = 2, $checked = 0, $name = "rdo_date", $text = g_l('cockpit', '[by_valid_until]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");

$date = new we_html_table(array(
		"cellpadding" => "0", "cellspacing" => "0", "border" => "0"
		), 3, 1);
$date->setCol(0, 0, array(
	"width" => 145
	), $oRdoDate[0]);
$date->setCol(1, 0, null, $oRdoDate[1]);
$date->setCol(2, 0, null, $oRdoDate[2]);

$parts[] = array(
	"headline" => g_l('cockpit', '[display_date]'), "html" => $date->getHTML(), "space" => 100
);

$oRdoPrio[0] = we_forms::radiobutton(
		$value = 0, $checked = 0, $name = "rdo_prio", $text = g_l('cockpit', '[high]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");
$oRdoPrio[1] = we_forms::radiobutton(
		$value = 1, $checked = 0, $name = "rdo_prio", $text = g_l('cockpit', '[medium]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");
$oRdoPrio[2] = we_forms::radiobutton(
		$value = 2, $checked = 1, $name = "rdo_prio", $text = g_l('cockpit', '[low]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");

$prio = new we_html_table(array(
		"cellpadding" => "0", "cellspacing" => "0", "border" => "0"
		), 3, 3);
$prio->setCol(0, 0, array(
	"width" => 70
	), $oRdoPrio[0]);
$prio->setCol(0, 1, null, we_html_tools::getPixel(10, 1));

$prio->setCol(0, 2, array(
	"width" => 20
	), we_html_element::htmlImg(array(
		"src" => IMAGE_DIR . "pd/prio_high.gif", "width" => 13, "height" => 14
	)));
$prio->setCol(1, 0, null, $oRdoPrio[1]);
$prio->setCol(1, 2, null, we_html_element::htmlImg(array(
		"src" => IMAGE_DIR . "pd/prio_medium.gif", "width" => 13, "height" => 14
	)));
$prio->setCol(2, 0, null, $oRdoPrio[2]);
$prio->setCol(2, 2, null, we_html_element::htmlImg(array(
		"src" => IMAGE_DIR . "pd/prio_low.gif", "width" => 13, "height" => 14
	)));

$parts[] = array(
	"headline" => g_l('cockpit', '[default_priority]'), "html" => $prio->getHTML(), "space" => 100
);

$oSctValid = we_html_tools::htmlSelect("sct_valid", array(
		g_l('cockpit', '[always]'), g_l('cockpit', '[from_date]'), g_l('cockpit', '[period]')
		), 1, g_l('cockpit', '[always]'), false, 'style="width:120px;" onChange=""', 'value', 120);

$parts[] = array(
	"headline" => g_l('cockpit', '[default_validity]'), "html" => $oSctValid, "space" => 100
);

list($pad_header_enc, ) = explode(',', $_REQUEST['we_cmd'][1]);
$pad_header = base64_decode($pad_header_enc);
$_sql = "SELECT	distinct(WidgetName) FROM " . NOTEPAD_TABLE . " WHERE UserID = " . intval($_SESSION['user']['ID']);
$DB_WE = new DB_WE();
$DB_WE->query($_sql);
$_options = array(
	$pad_header => $pad_header, g_l('cockpit', '[change]') => g_l('cockpit', '[change]')
);
while($DB_WE->next_record()) {
	$_options[$DB_WE->f('WidgetName')] = $DB_WE->f('WidgetName');
}
$oSctTitle = we_html_tools::htmlSelect("sct_title", array_unique($_options), 1, "", false, 'id="title" onChange=""', 'value');
$parts[] = array(
	"headline" => g_l('cockpit', '[title]'), "html" => $oSctTitle, "space" => 100
);
$parts[] = array(
	"headline" => g_l('cockpit', '[bg_color]'), "html" => $oSctCls->getHTML(), "space" => 100
);

$save_button = we_button::create_button("save", "javascript:save();", false, -1, -1);
$preview_button = we_button::create_button("preview", "javascript:preview();", false, -1, -1);
$cancel_button = we_button::create_button("close", "javascript:exit_close();");
$buttons = we_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

print we_html_element::htmlDocType() . we_html_element::htmlHtml(
		we_html_element::htmlHead(
			we_html_tools::getHtmlInnerHead(g_l('cockpit', '[notepad]')) . STYLESHEET . we_html_element::cssElement(
				"select{border:#AAAAAA solid 1px}") . we_html_element::jsScript(JS_DIR . "we_showMessage.js") .
			we_html_element::jsScript(JS_DIR . "weCombobox.js") .
			we_html_element::jsElement($jsPrefs . $jsCode)) . we_html_element::htmlBody(
			array(
			"class" => "weDialogBody", "onload" => "init();"
			), we_html_element::htmlForm(
				array(
				"onsubmit" => "return false;"
				), we_multiIconBox::getHTML(
					"padProps", "100%", $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[notepad]')))) . we_html_element::jsElement(
			"ComboBox=new weCombobox();ComboBox.init('title');"));
