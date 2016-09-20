<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes = [
	new weTagData_textAttribute('name', true, ''),
	new weTagData_choiceAttribute('num_format', [new weTagDataOption('german'),
		new weTagDataOption('french'),
		new weTagDataOption('english'),
		new weTagDataOption('swiss'),
		], false, false, ''),
	new weTagData_textAttribute('decimals', true, ''),
];
