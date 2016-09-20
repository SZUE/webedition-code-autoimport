<?php
$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
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
	new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_textAttribute('format', false, ''),
	new weTagData_choiceAttribute('num_format', [new weTagDataOption('german'),
		new weTagDataOption('french'),
		new weTagDataOption('english'),
		new weTagDataOption('swiss'),
		], false, false, ''),
	new weTagData_textAttribute('precision', false, ''),
];
