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
$jsCode = "
var _oCsv_;
var _sInitCsv_;
var _sUpbInc='upb/upb';
var _bPrev=false;
var _sLastPrevCsv='';

function init(){
	_fo=document.forms[0];
	_oCsv_=opener.gel(_sObjId+'_csv')
	var sCsv=_oCsv_.value;
	_sInitCsv_=sCsv;
	var oChbxType=_fo.elements.chbx_type;
	var iChbxTypeLen=oChbxType.length;
	if(iChbxTypeLen!=undefined){
		for(var i=iChbxTypeLen-1;i>=0;i--){
			oChbxType[i].checked=(parseInt(sCsv.charAt(i)))?true:false;
		}
	}else{
		oChbxType.checked=(parseInt(sCsv.charAt(0)))?true:false;
	}
	initPrefs();
}

function getBinary(){
	var sBinary='';
	var oChbx=_fo.elements.chbx_type;
";

if(defined('FILE_TABLE') && defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTFILES")){
	$jsCode .= "
	var iChbxLen=oChbx.length;
	for(var i=0;i<iChbxLen;i++){
		sBinary+=(oChbx[i].checked)?'1':'0';
	}
";
} else {
	$jsCode .= "
	sBinary+=(oChbx.checked)?'10':'00';
";
}

$jsCode .= "
	return sBinary;
}

function save(){
	var sCsv=getBinary();
	_oCsv_.value=sCsv;
	if((!_bPrev&&_sInitCsv_!=sCsv)||(_bPrev&&_sLastPrevCsv!=sCsv)){
		opener.rpc(sCsv,'','','','',_sObjId,_sUpbInc);
	}
	previewPrefs();
	top.we_showMessage(WE().consts.g_l.main.prefs_saved_successfully, WE().consts.message.WE_MESSAGE_NOTICE, window);
	self.close();
}

function preview(){
	_bPrev=true;
	var sCsv=getBinary();
	_sLastPrevCsv=sCsv;
	previewPrefs();
	opener.rpc(sCsv,'','','','',_sObjId,_sUpbInc);
}

function exit_close(){
	if(_sInitCsv_!=getBinary()&&_bPrev){
		opener.rpc(_sInitCsv_,'','','','',_sObjId,_sUpbInc);
	}
	exitPrefs();
	self.close();
}
";

$oChbxDocs = we_html_forms::checkbox(0, true, "chbx_type", g_l('cockpit', '[documents]'), true, "defaultfont", "", false, "", 0, 0);
$oChbxObjs = we_html_forms::checkbox(0, true, "chbx_type", g_l('cockpit', '[objects]'), true, "defaultfont", "", false, "", 0, 0);

$dbTableType = "<table><tr>" .
	(defined('FILE_TABLE') ? "<td>" . $oChbxDocs . "</td><td>" . we_html_tools::getPixel(10, 1) . "</td>" : '') .
	(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTFILES") ? "<td>" . $oChbxObjs . "</td>" : '') .
	"</tr></table>";

$parts = array(
	array(
		"headline" => g_l('cockpit', '[type]'), "html" => $dbTableType, "space" => 80
	),
	array(
		"headline" => "", "html" => $oSelCls->getHTML(), "space" => 0
	),
);

$save_button = we_html_button::create_button(we_html_button::SAVE, "javascript:save();", false, 0, 0);
$preview_button = we_html_button::create_button(we_html_button::PREVIEW, "javascript:preview();", false, 0, 0);
$cancel_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:exit_close();");
$buttons = we_html_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

$sTblWidget = we_html_multiIconBox::getHTML("mfdProps", $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[unpublished]'));

echo we_html_tools::getHtmlTop(g_l('cockpit', '[unpublished]'), '', '', STYLESHEET .
	we_html_element::jsElement(
		$jsPrefs . $jsCode), we_html_element::htmlBody(
		array(
		"class" => "weDialogBody", "onload" => "init();"
		), we_html_element::htmlForm("", $sTblWidget)));
