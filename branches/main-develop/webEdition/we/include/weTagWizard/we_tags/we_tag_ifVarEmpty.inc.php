<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new weTagData_textAttribute('match', true, ''),
	new weTagData_selectAttribute('doc', [new weTagDataOption('self'),
		new weTagDataOption('top'),
		new weTagDataOption('document'),
		new weTagDataOption('object'),
		], false, ''),
	new weTagData_selectAttribute('type', [new weTagDataOption('request'),
		new weTagDataOption('post'),
		new weTagDataOption('get'),
		new weTagDataOption('global'),
		new weTagDataOption('session'),
		new weTagDataOption('sessionfield'),
		new weTagDataOption('href'),
		new weTagDataOption('img'),
		new weTagDataOption('multiobject', false, 'object')], false, ''),
	new weTagData_selectAttribute('property', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_textAttribute('formname', false, ''),
];
