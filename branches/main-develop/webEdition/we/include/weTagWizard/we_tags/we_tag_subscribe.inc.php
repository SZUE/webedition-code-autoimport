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

$size = new weTagData_textAttribute('size', false, '');
$maxlength = new weTagData_textAttribute('maxlength', false, '');
$value = new weTagData_textAttribute('value', false, '');
$values = new weTagData_textAttribute('values', false, '');
$class = new weTagData_textAttribute('class', false, '');
$style = new weTagData_textAttribute('style', false, '');
$onchange = new weTagData_textAttribute('onchange', false, '');
$checked = new weTagData_selectAttribute('checked', weTagData_selectAttribute::getTrueFalse(), false, '');
$xml = new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, '');

$this->TypeAttribute = new weTagData_typeAttribute('type', [new weTagDataOption('email', false, 'newsletter', [$size, $maxlength, $value, $class, $style, $onchange], []),
	new weTagDataOption('htmlCheckbox', false, 'newsletter', [$class, $style, $checked], []),
	new weTagDataOption('htmlSelect', false, 'newsletter', [$value, $values, $class, $style], []),
	new weTagDataOption('firstname', false, 'newsletter', [$size, $maxlength, $value, $class, $style, $onchange], []),
	new weTagDataOption('lastname', false, 'newsletter', [$size, $maxlength, $value, $class, $style, $onchange], []),
	new weTagDataOption('salutation', false, 'newsletter', [$size, $maxlength, $value, $values, $class, $style, $onchange], []),
	new weTagDataOption('title', false, 'newsletter', [$size, $maxlength, $value, $values, $class, $style, $onchange], []),
	new weTagDataOption('listCheckbox', false, 'newsletter', [$class, $style, $checked], []),
	new weTagDataOption('listSelect', false, 'newsletter', [$size, $values, $class, $style], [])], false, '');

$this->Attributes = [$size, $maxlength, $value, $values, $class, $style, $onchange, $checked, $xml];
