<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes = array(
	new weTagData_textAttribute('name', true, ''),
	new weTagData_selectAttribute('reference', array(new weTagDataOption('article'),
		new weTagDataOption('cart'),
		), true, ''),
	new weTagData_textAttribute('shopname', true, ''),
	new weTagData_selectAttribute('type', array(
		new weTagDataOption('checkbox'),
		new weTagDataOption('choice'),
		new weTagDataOption('country'),
		new weTagDataOption('hidden'),
		new weTagDataOption('language'),
		new weTagDataOption('print'),
		new weTagDataOption('radio'),
		new weTagDataOption('select'),
		new weTagDataOption('textarea'),
		new weTagDataOption('textinput'),
		), false, ''),
	new weTagData_textAttribute('value', false, ''),
	new weTagData_textAttribute('values', false, ''),
	new weTagData_choiceAttribute('checked', weTagData_selectAttribute::getTrueFalse(), false, false, ''),
	new weTagData_choiceAttribute('mode', array(new weTagDataOption('add'),), false, false, ''),
	new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, ''),
);
