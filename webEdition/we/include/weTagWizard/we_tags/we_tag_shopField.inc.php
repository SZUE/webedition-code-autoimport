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




$name = new we_tagData_textAttribute('name', true, '');
$ref = new we_tagData_selectAttribute('reference', [new we_tagData_option('article'),
	new we_tagData_option('cart'),
	], true, '');
$shopname = new we_tagData_textAttribute('shopname', true, '');

$value = new we_tagData_textAttribute('value', false, '');
$values = new we_tagData_textAttribute('values', false, '');
$checked = new we_tagData_choiceAttribute('checked', we_tagData_selectAttribute::getTrueFalse(), false, false, '');
$add = new we_tagData_choiceAttribute('mode', [new we_tagData_option('add'),], false, false, '');
$xml = new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, '');
$ascountry = new we_tagData_selectAttribute('ascountry', we_tagData_selectAttribute::getTrueFalse(), false, '');
$aslanguage = new we_tagData_selectAttribute('aslanguage', we_tagData_selectAttribute::getTrueFalse(), false, '');


$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('checkbox', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	new we_tagData_option('choice', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	new we_tagData_option('country', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	new we_tagData_option('hidden', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	new we_tagData_option('language', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	new we_tagData_option('print', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $ascountry, $aslanguage, $xml]),
	new we_tagData_option('radio', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	new we_tagData_option('select', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	new we_tagData_option('textarea', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	new we_tagData_option('textinput', false, '', [$name, $ref, $shopname, $value, $values, $checked, $add, $xml]),
	]
	, false, '');

$this->Attributes = [$name, $ref, $shopname, $value, $values, $checked, $add, $xml];
