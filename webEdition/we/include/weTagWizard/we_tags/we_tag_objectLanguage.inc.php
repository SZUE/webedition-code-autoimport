<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'if_tags';
$this->Module = 'object';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new weTagData_selectAttribute('type', [new weTagDataOption('complete'),
		new weTagDataOption('language'),
		new weTagDataOption('country'),
		], false, ''),
	new weTagData_selectAttribute('case', [new weTagDataOption('unchanged'),
		new weTagDataOption('uppercase'),
		new weTagDataOption('lowercase'),
		], false, '')
];
