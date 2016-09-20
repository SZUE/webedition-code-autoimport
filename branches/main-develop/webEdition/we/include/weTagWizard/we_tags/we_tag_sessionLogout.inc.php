<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	(defined('FILE_TABLE') ? new weTagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, true, '') : null),
	new weTagData_choiceAttribute('target', [new weTagDataOption('_top'),
		new weTagDataOption('_parent'),
		new weTagDataOption('_self'),
		new weTagDataOption('_blank'),
		], false, false, ''),
	new weTagData_textAttribute('class', false, ''),
	new weTagData_textAttribute('style', false, ''),
];
