<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new weTagData_textAttribute('id', true, ''),
	new weTagData_textAttribute('show', false, ''),
	new weTagData_selectAttribute('doc', [new weTagDataOption('top'),
		new weTagDataOption('self'),
	 ], false, ''),
];
