<?php

$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'voting';

$this->Attributes[] = new weTagData_selectAttribute('name', array(new weTagDataOption('question'),
	new weTagDataOption('answer'),
	new weTagDataOption('result'),
	new weTagDataOption('id'),
	new weTagDataOption('date'),
	), true, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('text'),
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
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_textAttribute('format', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('num_format', array(new weTagDataOption('german'),
	new weTagDataOption('french'),
	new weTagDataOption('english'),
	new weTagDataOption('swiss'),
	), false, false, '');
$this->Attributes[] = new weTagData_textAttribute('precision', false, '');
