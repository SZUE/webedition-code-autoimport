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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */


require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/html/we_multibox.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/html/we_button.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/html/we_forms.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/we_class.inc.php');

include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_language/' . $GLOBALS['WE_LANGUAGE'] . '/modules/shop.inc.php');

//require_once(WE_SHOP_MODULE_DIR . 'weShopVatRule.class.php');
require_once(WE_SHOP_MODULE_DIR . 'weShopStatusMails.class.php');
protect();
htmlTop();

print STYLESHEET;

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
				we_submitForm("' . $_SERVER['PHP_SELF'] . '");
			break;
		}
	}
';



// initialise the shopStatusMails Object
if (isset($_REQUEST['we_cmd']) && $_REQUEST['we_cmd'][0] == 'saveShopStatusMails') {
	//p_r($_REQUEST);
	// initialise the vatRule by request
	$weShopStatusMails = weShopStatusMails::initByRequest($_REQUEST);
	$weShopStatusMails->save();

} else {

	$weShopStatusMails = weShopStatusMails::getShopStatusMails();
}

// array with all rules
$we_button = new we_button();

$ignoreFields = array('ID', 'Forename', 'Surname', 'Password', 'Username', 'ParentID', 'Path' ,'IsFolder', 'Icon', 'Text');
$customerTableFields = $DB_WE->metadata(CUSTOMER_TABLE);
$selectFields['-'] = '-';
foreach ($customerTableFields as $tblField) {
	if (!in_array($tblField['name'], $ignoreFields)) {
		$selectFields[$tblField['name']] = $tblField['name'];
	}
}

$parts = array();



$tabStatus = new we_htmlTable(array("border"=>"0", "cellpadding"=>"2", "cellspacing"=>"4"), $rows_num = 4, $cols_num = 10);
$i=0;
$tabStatus->setCol($i, 0, array("class"=>"defaultfont","style"=>"font-weight:bold","nowrap"=>"nowrap","width"=>110), $l_shop['statusmails']['fieldname']);

foreach ($weShopStatusMails->StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($i, $fieldkey+1, array("class"=>"defaultfont","style"=>"font-weight:bold","nowrap"=>"nowrap","width"=>120),  $fieldname);
}
$i++;
$tabStatus->setCol($i, 0, array("class"=>"defaultfont","style"=>"font-weight:bold","nowrap"=>"nowrap"), $l_shop['statusmails']['hidefield']);
foreach ($weShopStatusMails->StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($i, $fieldkey+1, array("class"=>"defaultfont","nowrap"=>"nowrap"), we_forms::checkboxWithHidden($weShopStatusMails->FieldsHidden[$fieldname], 'FieldsHidden['.$fieldname.']', $l_shop['statusmails']['hidefieldJa'],false,"defaultfont") );
}
$i++;
$tabStatus->setCol($i, 0, array("class"=>"defaultfont","style"=>"font-weight:bold","nowrap"=>"nowrap"), $l_shop['statusmails']['fieldtext']);
foreach ($weShopStatusMails->StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($i, $fieldkey+1, array("class"=>"defaultfont","nowrap"=>"nowrap"),'<input name="FieldsText['.$fieldname.']" size="15" type="text" value="'.$weShopStatusMails->FieldsText[$fieldname].'" />'   );
}
$i++;
$tabStatus->setCol($i, 0, array("class"=>"defaultfont","style"=>"font-weight:bold","nowrap"=>"nowrap"), $l_shop['statusmails']['EMailssenden']);
foreach ($weShopStatusMails->StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($i, $fieldkey+1, array("class"=>"defaultfont","nowrap"=>"nowrap"), we_class::htmlRadioButton('FieldsMails['.$fieldname.']',0,($weShopStatusMails->FieldsMails[$fieldname]==0?'1':'0'),'',$l_shop['statusmails']['EMailssendenNein'],'right').we_class::htmlRadioButton('FieldsMails['.$fieldname.']',1,($weShopStatusMails->FieldsMails[$fieldname]==1?'1':'0'),'',$l_shop['statusmails']['EMailssendenHand'],'right').we_class::htmlRadioButton('FieldsMails['.$fieldname.']',2,($weShopStatusMails->FieldsMails[$fieldname]==2?'1':'0'),'',$l_shop['statusmails']['EMailssendenAuto'],'right')   );
}
array_push($parts, array(
			'headline' => $l_shop['statusmails']['AnzeigeDaten'],
			'space' => 100,
			'html' =>'',
			'noline' => 1
		)
	);
array_push($parts, array(
			'headline' => '',
			'space' => 0,
			'html' =>$tabStatus->getHtmlCode()
		)
	);

$tabEMail = new we_htmlTable(array("border"=>"0", "cellpadding"=>"2", "cellspacing"=>"4"), $rows_num = 3, $cols_num = 6);
$tabEMail->setCol(0, 0, array("class"=>"defaultfont","nowrap"=>"nowrap","width"=> 220),  $l_shop['statusmails']['AbsenderAdresse'].'<br/>'.we_class::htmlTextInput("EMailData[address]",30,$weShopStatusMails->EMailData['address']) );
$tabEMail->setCol(1, 0, array("class"=>"defaultfont","nowrap"=>"nowrap","width"=> 220),  $l_shop['statusmails']['AbsenderName'].'<br/>'.we_class::htmlTextInput("EMailData[name]",30,$weShopStatusMails->EMailData['name']) );
$tabEMail->setCol(2, 0, array("class"=>"defaultfont","nowrap"=>"nowrap","width"=> 220),  $l_shop['statusmails']['bcc'].'<br/>'.we_class::htmlTextInput("EMailData[bcc]",30,$weShopStatusMails->EMailData['bcc']) );
$tabEMail->setCol(0, 1, array("class"=>"defaultfont","nowrap"=>"nowrap","width"=> 340),  $l_shop['statusmails']['EMailFeld'].'<br/>'.we_class::htmlSelect('EMailData[emailField]', $selectFields, 1, $weShopStatusMails->EMailData['emailField']) );
$tabEMail->setCol(1, 1, array("class"=>"defaultfont","nowrap"=>"nowrap","width"=> 340),  $l_shop['statusmails']['TitelFeld'].'<br/>'.we_class::htmlSelect('EMailData[titleField]', $selectFields, 1, $weShopStatusMails->EMailData['titleField']) );
$tabEMail->setCol(2, 1, array("class"=>"defaultfont","nowrap"=>"nowrap","width"=> 340),  $l_shop['statusmails']['DocumentSubjectField'].'<br/>'.we_class::htmlTextInput("EMailData[DocumentSubjectField]",30,$weShopStatusMails->EMailData['DocumentSubjectField']));

	
array_push($parts, array(
			'headline' => $l_shop['statusmails']['EMailDaten'],
			'space' => 110,
			'html' =>$tabEMail->getHtmlCode(),
			
		)
	);

$tabSprache = new we_htmlTable(array("border"=>"0", "cellpadding"=>"2", "cellspacing"=>"4"), $rows_num = 2, $cols_num = 5);
$tabSprache->setCol(0, 0, array("class"=>"defaultfont","nowrap"=>"nowrap","width"=> 220),  we_forms::checkboxWithHidden($weShopStatusMails->LanguageData['useLanguages'], 'LanguageData[useLanguages]', $l_shop['statusmails']['useLanguages'],false,"defaultfont"));
$tabSprache->setCol(0, 2, array("class"=>"defaultfont","nowrap"=>"nowrap","width"=> 220),  $l_shop['statusmails']['SprachenFeld'].we_class::htmlSelect('LanguageData[languageField]', $selectFields, 1, $weShopStatusMails->LanguageData['languageField']).we_forms::checkboxWithHidden($weShopStatusMails->LanguageData['languageFieldIsISO'], 'LanguageData[languageFieldIsISO]', $l_shop['preferences']['ISO-Kodiert'],false,"defaultfont") );

array_push($parts, array(
			'headline' => $l_shop['statusmails']['Spracheinstellungen'],
			'space' => 110,
			'html' =>$tabSprache->getHtmlCode(),
			'noline' => 1
		)
	);
array_push($parts, array(
			'html' => htmlAlertAttentionBox($l_shop['statusmails']['hintISO'],2,600),
			'space' => 0
		)
	);
$tabDokumente = new we_htmlTable(array("border"=>"0", "cellpadding"=>"2", "cellspacing"=>"4"), $rows_num = 2, $cols_num = 10);
$i=0;
$tabDokumente->setCol($i, 0, array("class"=>"defaultfont","style"=>"font-weight:bold","nowrap"=>"nowrap","width"=>110), $l_shop['statusmails']['fieldname']);

foreach ($weShopStatusMails->StatusFields as $fieldkey => $fieldname){
	$tabDokumente->setCol($i, $fieldkey+1, array("class"=>"defaultfont","style"=>"font-weight:bold","nowrap"=>"nowrap","width"=>120),  $fieldname);
}
$i++;
$tabDokumente->setCol($i, 0, array("class"=>"defaultfont","style"=>"font-weight:bold","nowrap"=>"nowrap"), $l_shop['statusmails']['defaultDocs']);
foreach ($weShopStatusMails->StatusFields as $fieldkey => $fieldname){
	$tabDokumente->setCol($i, $fieldkey+1, array("class"=>"defaultfont","nowrap"=>"nowrap"), we_class::htmlTextInput("FieldsDocuments[default][".$fieldname."]",15,$weShopStatusMails->FieldsDocuments['default'][$fieldname]) );
}
$frontendL = array_keys($GLOBALS["weFrontendLanguages"]);
	foreach ($frontendL as $lc => &$lcvalue){
		$lccode = explode('_', $lcvalue);
		$lcvalue= $lccode[0];
	}
foreach ($frontendL as $langkey){
	$tabDokumente->addRow();
	$i++;
	$tabDokumente->setCol($i, 0, array("class"=>"defaultfont","style"=>"font-weight:bold","nowrap"=>"nowrap"), $GLOBALS['l_languages'][$langkey].' ('.$langkey.')');
	foreach ($weShopStatusMails->StatusFields as $fieldkey => $fieldname){
		$tabDokumente->setCol($i, $fieldkey+1, array("class"=>"defaultfont","nowrap"=>"nowrap"), we_class::htmlTextInput("FieldsDocuments[".$langkey."][".$fieldname."]",15,$weShopStatusMails->FieldsDocuments[$langkey][$fieldname]) );
	}
}

array_push($parts, array(
			'headline' => $l_shop['statusmails']['Dokumente'],
			'space' => 100,
			'html' =>'',
			'noline' => 1
		)
	);
array_push($parts, array(
			'headline' => '',
			'space' => 0,
			'html' =>$tabDokumente->getHtmlCode()
		)
	);

print "
	<script type=\"text/javascript\">
		$jsFunction
	</script>
";


print '</head>
<body class="weDialogBody" onload="window.focus();">
	<form name="we_form" method="post">
	<input type="hidden" name="we_cmd[0]" value="saveShopStatusMails" />
';

print we_multiIconBox::getHTML(
	'weShopStatusMails',
	"1300",
	$parts,
	30,
	$we_button->position_yes_no_cancel(
		$we_button->create_button('save', 'javascript:we_cmd(\'save\');'),
		'',
		$we_button->create_button('cancel', 'javascript:we_cmd(\'close\');')
	),
	-1,
	'',
	'',
	false,
	$l_shop['statusmails']['box_headline'],'',741
);


print '
	</form>
</body>
</html>';

?>