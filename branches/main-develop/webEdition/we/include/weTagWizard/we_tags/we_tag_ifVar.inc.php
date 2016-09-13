<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new weTagData_textAttribute('name', true, ''),
	new weTagData_textAttribute('match', true, ''),
	new weTagData_selectAttribute('type', [new weTagDataOption('global'),
		new weTagDataOption('request'),
		new weTagDataOption('post'),
		new weTagDataOption('get'),
		new weTagDataOption('document'),
		new weTagDataOption('property'),
		new weTagDataOption('session'),
		new weTagDataOption('sessionfield'),
		], false, ''),
	new weTagData_selectAttribute('doc', [new weTagDataOption('self'),
		new weTagDataOption('top'),
		], false, ''),
	new weTagData_selectAttribute('operator', [new weTagDataOption('equal'),
		new weTagDataOption('less'),
		new weTagDataOption('less|equal'),
		new weTagDataOption('greater'),
		new weTagDataOption('greater|equal'),
		new weTagDataOption('contains'),
		new weTagDataOption('isin'),
		], false, ''),
];
