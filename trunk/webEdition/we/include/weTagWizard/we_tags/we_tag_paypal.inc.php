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

$this->Attributes[] = new weTagData_textAttribute('shopname', true, '');
$this->Attributes[] = new weTagData_textAttribute('pricename', true, '');
$this->Attributes[] = new weTagData_selectAttribute('usevat', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('netprices', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_textAttribute('countrycode', false, '');
$this->Attributes[] = new weTagData_textAttribute('languagecode', false, '');
$this->Attributes[] = new weTagData_textAttribute('shipping', false, '');
$this->Attributes[] = new weTagData_textAttribute('shippingisnet', false, '');
$this->Attributes[] = new weTagData_textAttribute('shippingvatrate', false, '');
$this->Attributes[] = new weTagData_selectAttribute('formtagonly', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_textAttribute('messageredirectAuto', false, '');
$this->Attributes[] = new weTagData_textAttribute('messageredirectMan', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('charset', array(new weTagDataOption('UTF-8'),
	new weTagDataOption('ISO-8859-1'),
	new weTagDataOption('ISO-8859-2'),
	new weTagDataOption('ISO-8859-3'),
	new weTagDataOption('ISO-8859-4'),
	new weTagDataOption('ISO-8859-5'),
	new weTagDataOption('ISO-8859-6'),
	new weTagDataOption('ISO-8859-7'),
	new weTagDataOption('ISO-8859-8'),
	new weTagDataOption('ISO-8859-9'),
	new weTagDataOption('ISO-8859-10'),
	new weTagDataOption('ISO-8859-11'),
	new weTagDataOption('ISO-8859-13'),
	new weTagDataOption('ISO-8859-14'),
	new weTagDataOption('ISO-8859-15'),
	new weTagDataOption('Windows-1251'),
	new weTagDataOption('Windows-1252'),
	), false, true, '');
$this->Attributes[] = new weTagData_textAttribute('currency', false, '');
