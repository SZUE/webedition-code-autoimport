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


include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_html_tools.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/html/we_button.inc.php");

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/we_class.inc.php');
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/html/we_multibox.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/html/we_forms.inc.php");

if(defined("SHOP_TABLE")){
	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/modules/shop.inc.php");
}

function prepareFieldname($str){

	if(strpos($str, '_')){
		return substr_replace($str, "/", strpos($str, '_'),1);
	} else {
		return $str;
	}

}

protect();

htmlTop();

print STYLESHEET;


$we_button = new we_button();
$ignoreFields = array('ID', 'Forename', 'Surname', 'Password', 'Username', 'ParentID', 'Path' ,'IsFolder', 'Icon', 'Text');
$customerTableFields = $DB_WE->metadata(CUSTOMER_TABLE);
$selectFields['-'] = '-';
foreach ($customerTableFields as $tblField) {
	if (!in_array($tblField['name'], $ignoreFields)) {
		$selectFields[$tblField['name']] = $tblField['name'];
	}
}

if(!empty($_REQUEST["format"])){	//	save data in arrays ..


	$_REQUEST['classID'] = isset($_REQUEST['classID']) ? trim($_REQUEST['classID']) : '';

	// check if field exists
	$q = 'SELECT 1 FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="shop_pref"';
	$DB_WE->query($q);
	if ( $DB_WE->num_rows() > 0) {
		$DB_WE->query("UPDATE ".ANZEIGE_PREFS_TABLE." SET strFelder= '" . $DB_WE->escape($_REQUEST["waehr"]) . "|" . $DB_WE->escape($_REQUEST["mwst"]) . "|" . $DB_WE->escape($_REQUEST["format"]) . "|" . $DB_WE->escape($_REQUEST["classID"]) . "|" . $DB_WE->escape($_REQUEST["pag"]) . "' WHERE strDateiname = 'shop_pref'");
	} else {
		$DB_WE->query("INSERT INTO ".ANZEIGE_PREFS_TABLE." (strFelder, strDateiname) VALUES ('" . $DB_WE->escape($_REQUEST["waehr"]) . "|" . $DB_WE->escape($_REQUEST["mwst"]) . "|" . $DB_WE->escape($_REQUEST["format"]) . "|" . $DB_WE->escape($_REQUEST["classID"]) . "|" . $DB_WE->escape($_REQUEST["pag"]) . "','shop_pref')" );
	}

	$fields['customerFields']      = isset($_REQUEST['orderfields']) ? $_REQUEST['orderfields'] : array();
	$fields['orderCustomerFields'] = isset($_REQUEST['ordercustomerfields']) ? $_REQUEST['ordercustomerfields'] : array();

	// check if field exists
	$q = 'SELECT 1 FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="edit_shop_properties"';
	$DB_WE->query($q);
	if ( $DB_WE->num_rows() > 0) {
		$DB_WE->query("UPDATE " . ANZEIGE_PREFS_TABLE . " SET strFelder = '" . $DB_WE->escape(serialize($fields)) . "' WHERE strDateiname ='edit_shop_properties'");
	} else {
		$DB_WE->query("INSERT INTO " . ANZEIGE_PREFS_TABLE . " (strFelder,strDateiname) VALUES('" . $DB_WE->escape(serialize($fields)) . "','edit_shop_properties')") ;
	}

	$CLFields['stateField'] = isset($_REQUEST['stateField']) ? $_REQUEST['stateField'] : '-';
	$CLFields['stateFieldIsISO'] = isset($_REQUEST['stateFieldIsISO']) ? $_REQUEST['stateFieldIsISO'] : 0;
	$CLFields['languageField'] = isset($_REQUEST['languageField']) ? $_REQUEST['languageField'] : '-';
	$CLFields['languageFieldIsISO'] = isset($_REQUEST['languageFieldIsISO']) ? $_REQUEST['languageFieldIsISO'] : 0;

	// check if field exists
	$q = 'SELECT 1 FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="shop_CountryLangauge"';
	$DB_WE->query($q);
	if ( $DB_WE->num_rows() > 0) {
		$DB_WE->query("UPDATE " . ANZEIGE_PREFS_TABLE . " SET strFelder = '" . $DB_WE->escape(serialize($CLFields)) . "' WHERE strDateiname ='shop_CountryLangauge'");
	} else {
		$DB_WE->query("INSERT INTO " . ANZEIGE_PREFS_TABLE . " (strFelder,strDateiname) VALUES('" . $DB_WE->escape(serialize($CLFields)) . "','shop_CountryLangauge')") ;
	}
	// Update Country Field in weShopVatRule
	require_once(WE_SHOP_MODULE_DIR . 'weShopVatRule.class.php');
	$weShopVatRule = weShopVatRule::getShopVatRule();
	$weShopVatRule->stateField = $CLFields['stateField'];
	$weShopVatRule->stateFieldIsISO = $CLFields['stateFieldIsISO'];
	$weShopVatRule->save();
	// Update Language Field in weShopStatusMails
	require_once(WE_SHOP_MODULE_DIR . 'weShopStatusMails.class.php');
	$weShopStatusMails = weShopStatusMails::getShopStatusMails();
	$weShopStatusMails->LanguageData['languageField'] = $CLFields['languageField'];
	$weShopStatusMails->LanguageData['languageFieldIsISO'] = $CLFields['languageFieldIsISO'];
	$weShopStatusMails->save();

	//	Close window when finished
	echo '<script type="text/javascript">self.close();</script>';
	exit;
} else {
	$q = 'SELECT 1 FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="shop_CountryLangauge"';
	$DB_WE->query($q);
	if ( $DB_WE->num_rows() > 0) {
		$DB_WE->next_record();
		$CLFields = unserialize($DB_WE->f("strFelder"));
	} else {
		$CLFields['stateField'] =  '-';
		$CLFields['stateFieldIsISO'] =  0;
		$CLFields['languageField'] =  '-';
		$CLFields['languageFieldIsISO'] =  0;
	}

}

	//	generate html-output table
	$_htmlTable = new we_htmlTable(	array(	'border'      => 0,
											'cellpadding' => 0,
											'cellspacing' => 0,
											'width' => "410"),
									35,
									3);


	//	NumberFormat - currency and taxes
	$DB_WE->query("SELECT strFelder from ".ANZEIGE_PREFS_TABLE." WHERE strDateiname = 'shop_pref'");
	$DB_WE->next_record();
	$feldnamen = explode("|",$DB_WE->f("strFelder"));

	if (isset($feldnamen[3])) {
		$fe = explode(",",$feldnamen[3]);
	} else {
		$fe = array();
	}
	if (!isset($feldnamen[4])){
	 $feldnamen[4]= "-";
	  }




	$_row = 0;
	$_htmlTable->setCol($_row, 0, array('class'=>'defaultfont'), $l_shop["waehrung"]);
	$_htmlTable->setColContent($_row, 1, getPixel(10,5) );
	$_htmlTable->setColContent($_row++, 2, htmlTextInput("waehr",6,$feldnamen[0],"","","text",50) );
	$_htmlTable->setCol($_row++, 0, array('colspan' => 4), getPixel(20,15));


	$_htmlTable->setCol($_row, 0, array('class'=>'defaultfont', 'valign'=>'top'), $l_shop["mwst"]);
	$_htmlTable->setColContent($_row, 1, getPixel(10,5) );
	$_htmlTable->setCol($_row++, 2, array('class'=>'defaultfont'), htmlTextInput("mwst",6,$feldnamen[1],"","","text",50) . '&nbsp%');
	$_htmlTable->setCol($_row++, 0, array('colspan' => 3), getPixel(5,5));
	$_htmlTable->setCol($_row++, 0, array('colspan' => 3, 'class' => 'small'), htmlAlertAttentionBox($l_shop["mwst_expl"], 2, "100%" , false, 100));
	$_htmlTable->setCol($_row++, 0, array('colspan' => 3), getPixel(20,15));

	$list = array("german" => "german","english" => "english","french" => "french", "swiss"=>"swiss");
	$_htmlTable->setCol($_row, 0, array('class'=>'defaultfont'), $l_shop["format"]);
	$_htmlTable->setColContent($_row, 1, getPixel(10,5) );
	$_htmlTable->setColContent($_row++, 2, htmlSelect('format', $list, 1, $feldnamen[2]) );
	$_htmlTable->setCol($_row++, 0, array('colspan' => 4), getPixel(20,15));


	$pager = array("default" => "-", "5" => "5", "10" => "10", "15" => "15" , "20" => "20", "25" => "25" ,"30" => "30", "35" => "35", "40" =>"40", "45" =>"45", "50" => "50");

	$_htmlTable->setCol($_row, 0, array('class'=>'defaultfont'), $l_shop["pageMod"]);
	$_htmlTable->setColContent($_row, 1, getPixel(10,5) );
	$_htmlTable->setColContent($_row++, 2, htmlSelect('pag', $pager, 1, $feldnamen[4]) );
	$_htmlTable->setCol($_row++, 0, array('colspan' => 4), getPixel(20,15));


	if (defined('OBJECT_TABLE')) {

		$_htmlTable->setCol($_row, 0, array('class'=>'defaultfont'), $l_shop["classID"]);
		$_htmlTable->setColContent($_row, 1, getPixel(10,5) );
		$_htmlTable->setColContent($_row++, 2, htmlTextInput("classID",6,(isset($feldnamen[3]) ? $feldnamen[3] : ''),"","","text",50). '<span class="small">&nbsp'. $l_shop["classIDext"].' </span>' );
		$_htmlTable->setCol($_row++, 0, array('colspan' => 4), getPixel(20,15));

	} else {


		$_htmlTable->setCol($_row++, 0, array('colspan' => 4), getPixel(20,15));
	}

	// look for all available fields in tblCustomer
	$DB_WE->query('SHOW FIELDS FROM ' . CUSTOMER_TABLE);
	$_availFields = array();

	while ($DB_WE->next_record()) {

		if (!in_array($DB_WE->f('Field'), $ignoreFields)) {
			$_availFields[$DB_WE->f('Field')] = prepareFieldname($DB_WE->f('Field'));
		}
	}
	asort($_availFields);

	//	get the already selected fields ...
	$DB_WE->query("SELECT strFelder from ".ANZEIGE_PREFS_TABLE." WHERE strDateiname = 'edit_shop_properties'");
	$DB_WE->next_record();
	$_entry = $DB_WE->f("strFelder");

	// ...
	if ($fields = @unserialize($_entry)) {
		// we have an array with following syntax:
		// array ( 'customerFields' => array('fieldname ...',...)
		//         'orderCustomerFields' => array('fieldname', ...) )

	} else {

		$fields['customerFields'] = array();
		$fields['orderCustomerFields'] = array();

		// the save format used to be ...
		// Vorname:tblWebUser||Forename,Nachname:tblWebUser||Surname,Contact/Address1:tblWebUser||Contact_Address1,Contact/Address1:tblWebUser||Contact_Address1,...
		$_fieldInfos = explode(",",$_entry);

		foreach ($_fieldInfos as $_fieldInfo) {

			$tmp1 = explode('||', $_fieldInfo);
			$tmp2 = explode(':',$tmp1[0]);

			$_fieldname = $tmp1[1];
			$_titel = $tmp2[0];
			$_tbl = $tmp2[1];

			if ($_tbl != 'webE') {
				$fields['customerFields'][] = $_fieldname;
			}

		}
		$fields['customerFields'] = array_unique($fields['customerFields']);

		unset($_tmpEntries);
	}

	$_htmlTable->setCol($_row, 0, array('class'=>'defaultfont', 'valign' => 'top'), $l_shop['preferences']['customerFields']);
	$_htmlTable->setColContent($_row, 1, getPixel(10,5) );
	$_htmlTable->setColContent($_row++, 2, htmlSelect('orderfields[]', $_availFields, (sizeof($_availFields) > 5 ? '5' : sizeof($_availFields)), implode(",", $fields['customerFields']), true, "", "value", 280 ) );
	$_htmlTable->setCol($_row++, 0, array('colspan' => 4), getPixel(20,15));

	$_htmlTable->setCol($_row, 0, array('class'=>'defaultfont', 'valign' => 'top'), $l_shop['preferences']['orderCustomerFields']);
	$_htmlTable->setColContent($_row, 1, getPixel(10,5) );
	$_htmlTable->setColContent($_row++, 2, htmlSelect('ordercustomerfields[]', $_availFields, (sizeof($_availFields) > 5 ? '5' : sizeof($_availFields)), implode(",", $fields['orderCustomerFields']), true, "", "value", 280 ) );
	$_htmlTable->setCol($_row++, 0, array('colspan' => 4), getPixel(20,15));

	$_htmlTable->setCol($_row, 0, array('class'=>'defaultfont', 'valign' => 'top'), $l_shop['preferences']['CountryField']);
	$_htmlTable->setColContent($_row, 1, getPixel(10,5) );

	$countrySelect = we_class::htmlSelect('stateField', $selectFields, 1, $CLFields['stateField']);
	$countrySelectISO = we_forms::checkboxWithHidden($CLFields['stateFieldIsISO'], 'stateFieldIsISO', $l_shop['preferences']['ISO-Kodiert'],false,"defaultfont");
	$_htmlTable->setColContent($_row++, 2, $countrySelect.'<br/>'.$countrySelectISO  );

	$_htmlTable->setCol($_row++, 0, array('colspan' => 4), getPixel(20,15));
	$_htmlTable->setCol($_row, 0, array('class'=>'defaultfont', 'valign' => 'top'), $l_shop['preferences']['LanguageField']);
	$languageSelect = we_class::htmlSelect('languageField', $selectFields, 1, $CLFields['languageField']);
	$languageSelectISO = we_forms::checkboxWithHidden($CLFields['languageFieldIsISO'], 'languageFieldIsISO', $l_shop['preferences']['ISO-Kodiert'],false,"defaultfont");
	$_htmlTable->setColContent($_row++, 2, $languageSelect.'<br/>'.$languageSelectISO  );
	$_htmlTable->setColContent($_row, 1, getPixel(10,5) );

	$_htmlTable->setCol($_row++, 0, array('colspan' => 4), getPixel(20,25));



	$_buttons = $we_button->position_yes_no_cancel(	$we_button->create_button("save", "javascript:document.we_form.submit();"),
													"",
													$we_button->create_button("cancel", "javascript:self.close();")
													);

	$frame = htmlDialogLayout($_htmlTable->getHtmlCode(), $l_shop["pref"], $_buttons);


echo '<script type="text/javascript">self.focus();</script>
	</head>
	<body class="weDialogBody">
	<form name="we_form" method="post" style="margin-left:8; margin-top:16px;">
	' . $frame. '</form>


 	</body></html>';


?>