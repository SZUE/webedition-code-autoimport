<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new weTagData_textAttribute('name', true, ''),
	new weTagData_choiceAttribute('type', [new weTagDataOption('textinput'),
		new weTagDataOption('textarea'),
		new weTagDataOption('select'),
		new weTagDataOption('radio'),
		new weTagDataOption('checkbox'),
		new weTagDataOption('country'),
		new weTagDataOption('language'),
		new weTagDataOption('file'),
		], false, true, ''),
	new weTagData_textAttribute('attribs', false, ''),
];
