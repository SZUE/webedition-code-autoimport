<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
*/
$this->NeedsEndTag = true;
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->DefaultValue = '<option>#1</option>
<option>#2</option>
<option>#3</option>';

$name = new we_tagData_textAttribute('name', true, '');
$size = new we_tagData_textAttribute('size', false, '');
$reload = new we_tagData_selectAttribute('reload', we_tagData_selectAttribute::getTrueFalse(), false, '');
$values = new we_tagData_textAttribute('values', false, '');
$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('', false, '', [$name, $size, $reload]),
	new we_tagData_option('html', false, '', [$name, $size, $reload]),
	new we_tagData_option('csv', false, '', [$name, $size, $reload, $values], [])], false, '');

$this->Attributes = [$name, $size, $reload, $values];
