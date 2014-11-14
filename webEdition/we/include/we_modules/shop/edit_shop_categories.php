<?php

/**
 * webEdition CMS
 *
 * $Rev: 8384 $
 * $Author: mokraemer $
 * $Date: 2014-10-07 18:49:11 +0200 (Di, 07 Okt 2014) $
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
$protect = we_base_moduleInfo::isActive('shop') && we_users_util::canEditModule('shop') ? null : array(false);
we_html_tools::protect($protect);

echo we_html_tools::getHtmlTop() . STYLESHEET;

$jsFunction = '
	function we_submitForm(url){
		var f = self.document.we_form;

		f.action = url;
		f.method = "post";
		f.submit();
	}

	function doUnload() {
		if (!!jsWindow_count) {
			for (i = 0; i < jsWindow_count; i++) {
				eval("jsWindow" + i + "Object.close()");
			}
		}
	}

	function we_cmd(){
		switch (arguments[0]) {
			case "close":
				window.close();
			break;

			case "save":
				we_submitForm("' . $_SERVER['SCRIPT_NAME'] . '");
			break;
		}
	}';

// initialise the shopStatusMails Object

$shopCatSelector = new we_html_table(array("border" => 0, "cellpadding" => 2, "cellspacing" => 4), $rows_num = 1, $cols_num = 2);

$parts = array(
	array(
		'headline' => 'Select Shop Categories\' Parent',
		'space' => 100,
		'html' => '',
		'noline' => 1
	),
	array(
		'headline' => '',
		'space' => 0,
		'html' => 'nothing much to see \'till now ;-)'
	),
);




$shopCategories = new we_html_table(array("border" => 0, "cellpadding" => 2, "cellspacing" => 4), $rows_num = 5, $cols_num = 17);
/*
$i = 0;
$tabStatus->setCol($i, 0, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap", "width" => 110), g_l('modules_shop', '[statusmails][fieldname]'));

foreach(we_shop_statusMails::$StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($i, $fieldkey + 1, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap", "width" => 120), $fieldname);
}
$i++;
$tabStatus->setCol($i, 0, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap"), g_l('modules_shop', '[statusmails][hidefield]'));
foreach(we_shop_statusMails::$StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($i, $fieldkey + 1, array("class" => "defaultfont", "nowrap" => "nowrap"), we_html_forms::checkboxWithHidden($weShopStatusMails->FieldsHidden[$fieldname], 'FieldsHidden[' . $fieldname . ']', g_l('modules_shop', '[statusmails][hidefieldJa]'), false, "defaultfont"));
}
$i++;
$tabStatus->setCol($i, 0, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap"), g_l('modules_shop', '[statusmails][hidefieldCOV]'));
foreach(we_shop_statusMails::$StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($i, $fieldkey + 1, array("class" => "defaultfont", "nowrap" => "nowrap"), we_html_forms::checkboxWithHidden($weShopStatusMails->FieldsHiddenCOV[$fieldname], 'FieldsHiddenCOV[' . $fieldname . ']', g_l('modules_shop', '[statusmails][hidefieldJa]'), false, "defaultfont"));
}
$i++;
$tabStatus->setCol($i, 0, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap"), g_l('modules_shop', '[statusmails][fieldtext]'));
foreach(we_shop_statusMails::$StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($i, $fieldkey + 1, array("class" => "defaultfont", "nowrap" => "nowrap"), '<input name="FieldsText[' . $fieldname . ']" size="15" type="text" value="' . $weShopStatusMails->FieldsText[$fieldname] . '" />');
}
$i++;
$tabStatus->setCol($i, 0, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap"), g_l('modules_shop', '[statusmails][EMailssenden]'));
foreach(we_shop_statusMails::$StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($i, $fieldkey + 1, array("class" => "defaultfont", "nowrap" => "nowrap"), we_html_forms::radioButton(0, ($weShopStatusMails->FieldsMails[$fieldname] == 0 ? '1' : '0'), 'FieldsMails[' . $fieldname . ']', g_l('modules_shop', '[statusmails][EMailssendenNein]')) .
		we_html_forms::radioButton(1, ($weShopStatusMails->FieldsMails[$fieldname] == 1 ? '1' : '0'), 'FieldsMails[' . $fieldname . ']', g_l('modules_shop', '[statusmails][EMailssendenHand]')) .
		we_html_forms::radioButton(2, ($weShopStatusMails->FieldsMails[$fieldname] == 2 ? '1' : '0'), 'FieldsMails[' . $fieldname . ']', g_l('modules_shop', '[statusmails][EMailssendenAuto]')));
}
 * 
 */
$parts[] = array(
	'headline' => 'Categories List',
	'space' => 100,
	'html' => '',
	'noline' => 1
);

$parts[] = array(
	'headline' => '',
	'space' => 0,
	'html' => $shopCategories->getHtml()
);



echo we_html_element::jsElement($jsFunction) .
 '</head>
<body class="weDialogBody" onload="window.focus();">
	<form name="we_form" method="post" >
	<input type="hidden" name="we_cmd[0]" value="saveShopStatusMails" />' .
 we_html_multiIconBox::getHTML(
	'weShopStatusMails', 700, $parts, 30, we_html_button::position_yes_no_cancel(
		we_html_button::create_button('save', 'javascript:we_cmd(\'save\');'), '', we_html_button::create_button('cancel', 'javascript:we_cmd(\'close\');')
	), -1, '', '', false, 'Define relations between shop categories and vat rates', '', '', 'scroll'
) . '</form>
</body>
</html>';
