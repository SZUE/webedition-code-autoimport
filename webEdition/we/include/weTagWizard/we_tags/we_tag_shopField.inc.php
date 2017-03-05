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




$name = new weTagData_textAttribute('name', true, '');
$ref = new weTagData_selectAttribute('reference', [new weTagDataOption('article'),
	new weTagDataOption('cart'),
	], true, '');
$shopname = new weTagData_textAttribute('shopname', true, '');

$value = new weTagData_textAttribute('value', false, '');
$values = new weTagData_textAttribute('values', false, '');
$checked = new weTagData_choiceAttribute('checked', weTagData_selectAttribute::getTrueFalse(), false, false, '');
$add = new weTagData_choiceAttribute('mode', [new weTagDataOption('add'),], false, false, '');
$xml = new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, '');
$ascountry = new weTagData_selectAttribute('ascountry', weTagData_selectAttribute::getTrueFalse(), false, '');
$aslanguage = new weTagData_selectAttribute('aslanguage', weTagData_selectAttribute::getTrueFalse(), false, '');


$this->TypeAttribute = new weTagData_typeAttribute('type', [new weTagDataOption('checkbox', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	new weTagDataOption('choice', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	new weTagDataOption('country', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	new weTagDataOption('hidden', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	new weTagDataOption('language', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	new weTagDataOption('print', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $ascountry, $aslanguage, $xml]),
	new weTagDataOption('radio', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	new weTagDataOption('select', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	new weTagDataOption('textarea', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	new weTagDataOption('textinput', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	]
	, false, '');

$this->Attributes = [$name, $ref, $shopname, $value, $values, $checked, $add, $xml];
