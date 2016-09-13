<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new weTagData_textAttribute('match', true),
	new weTagData_selectAttribute('type', [new weTagDataOption('id'), new weTagDataOption('name')], true),
	new weTagData_textAttribute('mandatory'),
	new weTagData_textAttribute('email'),
	new weTagData_textAttribute('password'),
	new weTagData_textAttribute('onError'),
	new weTagData_textAttribute('jsIncludePath'),
];
