<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new weTagData_textAttribute('name', true, ''),
	new weTagData_selectAttribute('type', [new weTagDataOption('global'),
		new weTagDataOption('request'),
		new weTagDataOption('session'),
		], false, ''),
	new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, ''),
];
