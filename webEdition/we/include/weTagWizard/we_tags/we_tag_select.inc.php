<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->DefaultValue = '<option>#1</option>
<option>#2</option>
<option>#3</option>';

$name = new weTagData_textAttribute('name', true, '');
$size = new weTagData_textAttribute('size', false, '');
$reload = new weTagData_selectAttribute('reload', weTagData_selectAttribute::getTrueFalse(), false, '');
$values = new weTagData_textAttribute('values', false, '');
$this->TypeAttribute = new weTagData_typeAttribute('type', array(
	new weTagDataOption('', false, '', array($name, $size, $reload)),
	new weTagDataOption('html', false, '', array($name, $size, $reload)),
	new weTagDataOption('csv', false, '', array($name, $size, $reload, $values), [])), false, '');

$this->Attributes = array($name, $size, $reload, $values);
