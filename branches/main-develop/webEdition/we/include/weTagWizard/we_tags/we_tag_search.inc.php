<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new weTagData_selectAttribute('type', [new weTagDataOption('textinput'),
		new weTagDataOption('textarea'),
		new weTagDataOption('print'),
	 ], false, ''),
	new weTagData_textAttribute('name', false, ''),
	new weTagData_textAttribute('value', false, ''),
	new weTagData_textAttribute('size', false, ''),
	new weTagData_textAttribute('maxlength', false, ''),
	new weTagData_textAttribute('cols', false, ''),
	new weTagData_textAttribute('rows', false, ''),
	new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, ''),
];
