<?php
$this->NeedsEndTag = true;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Groups[] = 'if_tags';
$this->Module = 'voting';

$this->Attributes = [
	new weTagData_selectAttribute('name', [new weTagDataOption('question'),
		new weTagDataOption('answer'),
		new weTagDataOption('result'),
		new weTagDataOption('id'),
		new weTagDataOption('date'),
		], true, ''),
	new weTagData_selectAttribute('type', [new weTagDataOption('text'),
		new weTagDataOption('radio'),
		new weTagDataOption('checkbox'),
		new weTagDataOption('select'),
		new weTagDataOption('count'),
		new weTagDataOption('percent'),
		new weTagDataOption('total'),
		new weTagDataOption('answer'),
		new weTagDataOption('voting'),
		new weTagDataOption('textinput'),
		new weTagDataOption('textarea'),
		new weTagDataOption('image'),
		new weTagDataOption('media'),
		], false, ''),
];
