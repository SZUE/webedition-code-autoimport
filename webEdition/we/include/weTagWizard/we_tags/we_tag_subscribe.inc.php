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
//$this->Groups[] = 'if_tags';
$this->Module = 'newsletter';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$size = new we_tagData_textAttribute('size', false, '');
$maxlength = new we_tagData_textAttribute('maxlength', false, '');
$value = new we_tagData_textAttribute('value', false, '');
$values = new we_tagData_textAttribute('values', false, '');
$class = new we_tagData_textAttribute('class', false, '');
$style = new we_tagData_textAttribute('style', false, '');
$onchange = new we_tagData_textAttribute('onchange', false, '');
$checked = new we_tagData_selectAttribute('checked', we_tagData_selectAttribute::getTrueFalse(), false, '');
$xml = new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, '');

$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('email', false, 'newsletter', [$size, $maxlength, $value, $class, $style, $onchange], []),
	new we_tagData_option('htmlCheckbox', false, 'newsletter', [$class, $style, $checked], []),
	new we_tagData_option('htmlSelect', false, 'newsletter', [$value, $values, $class, $style], []),
	new we_tagData_option('firstname', false, 'newsletter', [$size, $maxlength, $value, $class, $style, $onchange], []),
	new we_tagData_option('lastname', false, 'newsletter', [$size, $maxlength, $value, $class, $style, $onchange], []),
	new we_tagData_option('salutation', false, 'newsletter', [$size, $maxlength, $value, $values, $class, $style, $onchange], []),
	new we_tagData_option('title', false, 'newsletter', [$size, $maxlength, $value, $values, $class, $style, $onchange], []),
	new we_tagData_option('listCheckbox', false, 'newsletter', [$class, $style, $checked], []),
	new we_tagData_option('listSelect', false, 'newsletter', [$size, $values, $class, $style], [])], false, '');

$this->Attributes = [$size, $maxlength, $value, $values, $class, $style, $onchange, $checked, $xml];
