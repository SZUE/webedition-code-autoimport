<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'navigation_tags';
$this->Module = 'navigation';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->DefaultValue = '<a href="<we:navigationField name="href" />"><we:navigationField name="text" /></a><br />';

$this->Attributes = [
	new weTagData_textAttribute('navigationname', false, ''),
	new weTagData_selectAttribute('type', [new weTagDataOption(weTagData_selectorAttribute::FOLDER),
		new weTagDataOption('item'),
		], true, ''),
	new weTagData_textAttribute('level', false, ''),
	new weTagData_selectAttribute('current', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_choiceAttribute('position', [new weTagDataOption('first'),
		new weTagDataOption('odd'),
		new weTagDataOption('even'),
		new weTagDataOption('last'),
		], false, false, ''),
];
