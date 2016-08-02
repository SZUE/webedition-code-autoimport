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
$protect = we_base_moduleInfo::isActive(we_base_moduleInfo::SHOP) && we_users_util::canEditModule(we_base_moduleInfo::SHOP) ? null : array(false);
we_html_tools::protect($protect);

echo we_html_tools::getHtmlTop() . STYLESHEET;

$jsFunction = '
	function we_submitForm(url){
		var f = self.document.we_form;
	if (!f.checkValidity()) {
		top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}
		f.action = url;
    	f.method = "post";

    	f.submit();
			return true;
    }

	function doUnload() {
		WE().util.jsWindow.prototype.closeAll(window);
	}

	function we_cmd(){
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

		switch (args[0]) {
			case "close":
				window.close();
				break;
			case "save":
				we_submitForm("' . $_SERVER['SCRIPT_NAME'] . '");
				break;
			default:
				top.opener.top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
		}
	}';

// initialise the shopStatusMails Object
if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === 'saveShopStatusMails'){
	// initialise the vatRule by request
	$weShopStatusMails = we_shop_statusMails::initByRequest();
	$weShopStatusMails->save();
} else {

	$weShopStatusMails = we_shop_statusMails::getShopStatusMails();
}

// array with all rules

$ignoreFields = array('ID', 'Forename', 'Surname', 'Password', 'Username', 'ParentID', 'Path', 'IsFolder', 'Text');
$customerTableFields = $DB_WE->metadata(CUSTOMER_TABLE);
$selectFields['-'] = '-';
foreach($customerTableFields as $tblField){
	if(!in_array($tblField['name'], $ignoreFields)){
		$selectFields[$tblField['name']] = $tblField['name'];
	}
}

$frontendL = $GLOBALS["weFrontendLanguages"];
foreach($frontendL as &$lcvalue){
	$lccode = explode('_', $lcvalue);
	$lcvalue = $lccode[0];
}
unset($lcvalue);

$tabStatus = new we_html_table(['class' => 'withSpace'], $rows_num = 5, $cols_num = 6 + count($frontendL));

$tabStatus->setCol(0, 1, ['colspan' => 4, 'class' => 'defaultfont bold', 'style' => 'text-align:center;background-color:yellow;'], g_l('modules_shop', '[statusmails][AnzeigeDaten]'));
$tabStatus->setCol(0, 5, ['colspan' => 1 + count($frontendL), 'class' => 'defaultfont bold', 'style' => 'text-align:center;background-color:lightblue;'], g_l('modules_shop', '[statusmails][Dokumente]'));
$tabStatus->setCol(1, 0, ['class' => 'defaultfont bold'], g_l('modules_shop', '[statusmails][fieldname]'));
$tabStatus->setCol(1, 1, ['class' => 'defaultfont bold'], g_l('modules_shop', '[statusmails][hidefield]'));
$tabStatus->setCol(1, 2, ['class' => "defaultfont bold"], g_l('modules_shop', '[statusmails][hidefieldCOV]'));
$tabStatus->setCol(1, 3, ['class' => "defaultfont bold"], g_l('modules_shop', '[statusmails][fieldtext]'));
$tabStatus->setCol(1, 4, ['class' => "defaultfont bold"], g_l('modules_shop', '[statusmails][EMailssenden]'));
$tabStatus->setCol(1, 5, ['class' => "defaultfont bold"], g_l('modules_shop', '[statusmails][defaultDocs]'));

foreach($frontendL as $pos => $langkey){
	$tabStatus->setCol(1, 6 + $pos, ['class' => "defaultfont bold"], g_l('languages', '[' . $langkey . ']') . ' (' . $langkey . ')');
}

foreach(we_shop_statusMails::$StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($fieldkey + 2, 0, array('class' => "defaultfont bold", "width" => 120), $fieldname);
}
foreach(we_shop_statusMails::$StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($fieldkey + 2, 1, ['class' => 'defaultfont'], we_html_forms::checkboxWithHidden($weShopStatusMails->FieldsHidden[$fieldname], 'FieldsHidden[' . $fieldname . ']', g_l('modules_shop', '[statusmails][hidefieldJa]'), false, "defaultfont"));
}

foreach(we_shop_statusMails::$StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($fieldkey + 2, 2, ['class' => 'defaultfont'], we_html_forms::checkboxWithHidden($weShopStatusMails->FieldsHiddenCOV[$fieldname], 'FieldsHiddenCOV[' . $fieldname . ']', g_l('modules_shop', '[statusmails][hidefieldJa]'), false, "defaultfont"));
}

foreach(we_shop_statusMails::$StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($fieldkey + 2, 3, ['class' => 'defaultfont'], '<input name="FieldsText[' . $fieldname . ']" size="15" type="text" value="' . $weShopStatusMails->FieldsText[$fieldname] . '" />');
}

foreach(we_shop_statusMails::$StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($fieldkey + 2, 4, ['class' => 'defaultfont'], we_html_forms::radioButton(0, ($weShopStatusMails->FieldsMails[$fieldname] == 0 ? '1' : '0'), 'FieldsMails[' . $fieldname . ']', g_l('modules_shop', '[statusmails][EMailssendenNein]')) .
		we_html_forms::radioButton(1, ($weShopStatusMails->FieldsMails[$fieldname] == 1 ? '1' : '0'), 'FieldsMails[' . $fieldname . ']', g_l('modules_shop', '[statusmails][EMailssendenHand]')) .
		we_html_forms::radioButton(2, ($weShopStatusMails->FieldsMails[$fieldname] == 2 ? '1' : '0'), 'FieldsMails[' . $fieldname . ']', g_l('modules_shop', '[statusmails][EMailssendenAuto]')));
}

foreach(we_shop_statusMails::$StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($fieldkey + 2, 5, ['class' => 'defaultfont'], we_html_tools::htmlTextInput("FieldsDocuments[default][" . $fieldname . "]", 15, $weShopStatusMails->FieldsDocuments['default'][$fieldname]));
}

foreach($frontendL as $pos => $langkey){
	foreach(we_shop_statusMails::$StatusFields as $fieldkey => $fieldname){
		$tabStatus->setCol($fieldkey + 2, 6 + $pos, ['class' => 'defaultfont'], we_html_tools::htmlTextInput('FieldsDocuments[' . $langkey . '][' . $fieldname . ']', 15, $weShopStatusMails->FieldsDocuments[$langkey][$fieldname]));
	}
}

$parts = [
	[
		'headline' => g_l('modules_shop', '[statusmails][AnzeigeDaten]'),
		'space' => we_html_multiIconBox::SPACE_MED,
		'html' => '',
		'noline' => 1
	],
	[
		'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[statusmails][hintDokumente]'), we_html_tools::TYPE_INFO, 650, false),
		'noline' => 1
	 ],
	[
		'headline' => '',
		'html' => $tabStatus->getHtml()
	],
];

$tabEMail = new we_html_table(['class' => 'withSpace'], $rows_num = 4, $cols_num = 6);
$tabEMail->setCol(0, 0, array('class' => 'defaultfont', "width" => 220), g_l('modules_shop', '[statusmails][AbsenderAdresse]') .
	'<br/>' . we_html_tools::htmlTextInput("EMailData[address]", 30, $weShopStatusMails->EMailData['address']));
$tabEMail->setCol(1, 0, array('class' => 'defaultfont', "width" => 220), g_l('modules_shop', '[statusmails][AbsenderName]') .
	'<br/>' . we_html_tools::htmlTextInput("EMailData[name]", 30, $weShopStatusMails->EMailData['name']));
$tabEMail->setCol(2, 0, array('class' => 'defaultfont', "width" => 220), g_l('modules_shop', '[statusmails][bcc]') .
	'<br/>' . we_html_tools::htmlTextInput("EMailData[bcc]", 30, $weShopStatusMails->EMailData['bcc']));
$tabEMail->setCol(0, 1, array('class' => 'defaultfont', "width" => 340), g_l('modules_shop', '[statusmails][EMailFeld]') .
	'<br/>' . we_class::htmlSelect('EMailData[emailField]', $selectFields, 1, $weShopStatusMails->EMailData['emailField']));
$tabEMail->setCol(1, 1, array('class' => 'defaultfont', "width" => 340), g_l('modules_shop', '[statusmails][TitelFeld]') .
	'<br/>' . we_class::htmlSelect('EMailData[titleField]', $selectFields, 1, $weShopStatusMails->EMailData['titleField']));
$tabEMail->setCol(2, 1, array('class' => 'defaultfont', "width" => 340), g_l('modules_shop', '[statusmails][DocumentSubjectField]') .
	'<br/>' . we_html_tools::htmlTextInput("EMailData[DocumentSubjectField]", 30, $weShopStatusMails->EMailData['DocumentSubjectField']));
$tabEMail->setCol(3, 0, array('class' => 'defaultfont', "width" => 340), g_l('modules_shop', '[statusmails][DocumentAttachmentFieldA]') . '<br/>' . we_html_tools::htmlTextInput("EMailData[DocumentAttachmentFieldA]", 30, $weShopStatusMails->EMailData['DocumentAttachmentFieldA']));
$tabEMail->setCol(3, 1, array('class' => 'defaultfont', "width" => 340), g_l('modules_shop', '[statusmails][DocumentAttachmentFieldB]') . '<br/>' . we_html_tools::htmlTextInput("EMailData[DocumentAttachmentFieldB]", 30, $weShopStatusMails->EMailData['DocumentAttachmentFieldB']));


$parts[] = array(
	'headline' => g_l('modules_shop', '[statusmails][EMailDaten]'),
	'html' => '',
	'space' => we_html_multiIconBox::SPACE_MED,
	'noline' => 1
);
$parts[] = array(
	'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[statusmails][hintEMailDaten]'), we_html_tools::TYPE_INFO, 650, false),
	'noline' => 1
);
$parts[] = array(
	'space' => we_html_multiIconBox::SPACE_MED,
	'html' => $tabEMail->getHtml(),
);

$tabSprache = new we_html_table(['class' => 'withSpace'], $rows_num = 2, $cols_num = 5);
$tabSprache->setCol(0, 0, array('class' => 'defaultfont', "width" => 220), we_html_forms::checkboxWithHidden($weShopStatusMails->LanguageData['useLanguages'], 'LanguageData[useLanguages]', g_l('modules_shop', '[statusmails][useLanguages]'), false, "defaultfont"));
$tabSprache->setCol(0, 2, array('class' => 'defaultfont', "width" => 220), g_l('modules_shop', '[statusmails][SprachenFeld]') . we_class::htmlSelect('LanguageData[languageField]', $selectFields, 1, $weShopStatusMails->LanguageData['languageField']) . we_html_forms::checkboxWithHidden($weShopStatusMails->LanguageData['languageFieldIsISO'], 'LanguageData[languageFieldIsISO]', g_l('modules_shop', '[preferences][ISO-Kodiert]'), false, "defaultfont"));

$parts[] = [
	'headline' => g_l('modules_shop', '[statusmails][Spracheinstellungen]'),
	'space' => we_html_multiIconBox::SPACE_MED,
	'html' => '',
	'noline' => 1
];
$parts[] = [
	'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[statusmails][hintSprache]'), we_html_tools::TYPE_INFO, 650, false),
	'noline' => 1
];
$parts[] = [
	'space' => we_html_multiIconBox::SPACE_MED,
	'html' => $tabSprache->getHtml(),
	'noline' => 1
];
$parts[] = [
	'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[statusmails][hintISO]'), we_html_tools::TYPE_INFO, 650, false),
];

echo we_html_element::jsElement($jsFunction);
?>
</head>
<body class="weDialogBody" onload="window.focus();">
	<form name="we_form" method="post" >
		<input type="hidden" name="we_cmd[0]" value="saveShopStatusMails" />
		<?=
		we_html_multiIconBox::getHTML('weShopStatusMails', $parts, 30, we_html_button::position_yes_no_cancel(
				we_html_button::create_button(we_html_button::SAVE, 'javascript:we_cmd(\'save\');'), '', we_html_button::create_button(we_html_button::CANCEL, 'javascript:we_cmd(\'close\');')
			), -1, '', '', false, g_l('modules_shop', '[statusmails][box_headline]'), '', '', 'scroll'
		);
		?>
	</form>
</body>
</html>