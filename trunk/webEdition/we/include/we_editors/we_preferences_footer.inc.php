<?php

/**
 * webEdition CMS
 *
 * $Rev: 4819 $
 * $Author: mokraemer $
 * $Date: 2012-08-06 18:59:06 +0200 (Mo, 06. Aug 2012) $
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
// Define needed JS
//$acErrorMsg = we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR);

function getPreferencesFooterJS(){
	$_javascript = <<< END_OF_SCRIPT
var countSaveTrys = 0;
function we_save() {

	document.getElementById('content').contentDocument.getElementById('setting_ui').style.display = 'none';
	document.getElementById('content').contentDocument.getElementById('setting_extensions').style.display = 'none';
	document.getElementById('content').contentDocument.getElementById('setting_editor').style.display = 'none';
	document.getElementById('content').contentDocument.getElementById('setting_recipients').style.display = 'none';
	document.getElementById('content').contentDocument.getElementById('setting_proxy').style.display = 'none';
	document.getElementById('content').contentDocument.getElementById('setting_advanced').style.display = 'none';
	document.getElementById('content').contentDocument.getElementById('setting_system').style.display = 'none';
	document.getElementById('content').contentDocument.getElementById('setting_seolinks').style.display = 'none';
	document.getElementById('content').contentDocument.getElementById('setting_error_handling').style.display = 'none';
	document.getElementById('content').contentDocument.getElementById('setting_backup').style.display = 'none';
	document.getElementById('content').contentDocument.getElementById('setting_validation').style.display = 'none';
	document.getElementById('content').contentDocument.getElementById('setting_language').style.display = 'none';
	document.getElementById('content').contentDocument.getElementById('setting_message_reporting').style.display = 'none';
	//document.getElementById('content').contentDocument.getElementById('setting_modules').style.display = 'none';

	// update setting for message_reporting
	top.opener.top.messageSettings = document.getElementById('content').contentDocument.getElementById("message_reporting").value;

	if(top.opener.top.weEditorFrameController.getActiveDocumentReference().quickstart){
		var oCockpit=top.opener.top.weEditorFrameController.getActiveDocumentReference();
		var _fo=document.getElementById('content').contentDocument.forms[0];
		var oSctCols=_fo.elements['newconf[cockpit_amount_columns]'];
		var iCols=oSctCols.options[oSctCols.selectedIndex].value;
//		if(iCols!=oCockpit._iLayoutCols){
//			oCockpit.modifyLayoutCols(iCols);
//		}
	}

	document.getElementById('content').contentDocument.getElementById('setting_email').style.display = 'none';
	document.getElementById('content').contentDocument.getElementById('setting_save').style.display = '';
	document.getElementById('content').contentDocument.we_form.save_settings.value = 'true';

END_OF_SCRIPT;

	if(we_hasPerm('FORMMAIL')){
		$_javascript .= '
		//document.content.send_recipients(); //FIX ME
		';
	}

// Define needed JS
	$_javascript .= <<< END_OF_SCRIPT
	document.getElementById('content').contentDocument.we_form.submit();
}

END_OF_SCRIPT;
	return we_html_element::jsElement($_javascript);
}

/* * ***************************************************************************
 * RENDER FILE
 * *************************************************************************** */

function getPreferencesFooter(){
	$okbut = we_button::create_button('save', 'javascript:we_save();');
	$cancelbut = we_button::create_button('cancel', 'javascript:top.close()');

	return we_html_element::htmlDiv(array('class' => 'weDialogButtonsBody', 'style' => 'height:100%;'), we_button::position_yes_no_cancel($okbut, "", $cancelbut, 10, "", "", 0));
}