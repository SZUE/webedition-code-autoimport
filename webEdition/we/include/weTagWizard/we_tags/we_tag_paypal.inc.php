<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
*/
$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes = [
	new we_tagData_textAttribute('shopname', true, ''),
	new we_tagData_textAttribute('pricename', true, ''),
	new we_tagData_selectAttribute('usevat', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_selectAttribute('netprices', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_textAttribute('countrycode', false, ''),
	new we_tagData_textAttribute('languagecode', false, ''),
	new we_tagData_textAttribute('shipping', false, ''),
	new we_tagData_textAttribute('shippingisnet', false, ''),
	new we_tagData_textAttribute('shippingvatrate', false, ''),
	new we_tagData_selectAttribute('formtagonly', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_textAttribute('messageredirectAuto', false, ''),
	new we_tagData_textAttribute('messageredirectMan', false, ''),
	new we_tagData_choiceAttribute('charset', [new we_tagData_option('UTF-8'),
		new we_tagData_option('ISO-8859-1'),
		new we_tagData_option('ISO-8859-2'),
		new we_tagData_option('ISO-8859-3'),
		new we_tagData_option('ISO-8859-4'),
		new we_tagData_option('ISO-8859-5'),
		new we_tagData_option('ISO-8859-6'),
		new we_tagData_option('ISO-8859-7'),
		new we_tagData_option('ISO-8859-8'),
		new we_tagData_option('ISO-8859-9'),
		new we_tagData_option('ISO-8859-10'),
		new we_tagData_option('ISO-8859-11'),
		new we_tagData_option('ISO-8859-13'),
		new we_tagData_option('ISO-8859-14'),
		new we_tagData_option('ISO-8859-15'),
		new we_tagData_option('Windows-1251'),
		new we_tagData_option('Windows-1252'),
	 ], false, true, ''),
	new we_tagData_textAttribute('currency', false, ''),
];
