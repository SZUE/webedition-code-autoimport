<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->DefaultValue = g_l('weTag', '[' . $tagName . '][defaultvalue]', true);

$this->Attributes = [
	(defined('FILE_TABLE') ? new weTagData_selectorAttribute('id', FILE_TABLE, '', false, '') : null),
	new weTagData_textAttribute('file', false, ''),
	new weTagData_textAttribute('url', false, ''),
	new weTagData_choiceAttribute('width', [new weTagDataOption('100'),
		new weTagDataOption('150'),
		new weTagDataOption('200'),
		new weTagDataOption('250'),
		new weTagDataOption('300'),
		new weTagDataOption('350'),
		new weTagDataOption('400'),
		], false, true, ''),
];
