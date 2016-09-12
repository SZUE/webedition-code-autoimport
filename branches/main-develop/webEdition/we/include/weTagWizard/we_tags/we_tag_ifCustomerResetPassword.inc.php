<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->TypeAttribute = new weTagData_typeAttribute('type', [new weTagDataOption('all'),
	new weTagDataOption('passwordMismatch'),
	new weTagDataOption('required'),
	new weTagDataOption('token'),
	], true, '');

