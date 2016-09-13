<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [ new weTagData_textAttribute('name', false, ''),
	new weTagData_selectAttribute('type', [new weTagDataOption('request'),], false, ''),
	new weTagData_selectAttribute('showpath', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_textAttribute('rootdir', false, ''),
	new weTagData_textAttribute('firstentry', false, ''),
	new weTagData_selectAttribute('multiple', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_textAttribute('indent', false, ''),
];
