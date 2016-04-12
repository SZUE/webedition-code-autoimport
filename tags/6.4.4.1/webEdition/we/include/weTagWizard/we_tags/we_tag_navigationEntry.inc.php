<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'navigation_tags';
$this->Module = 'navigation';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->DefaultValue = '<a href="<we:navigationField name="href" />"><we:navigationField name="text" /></a><br />';

$this->Attributes[] = new weTagData_textAttribute('navigationname', false, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption(weTagData_selectorAttribute::FOLDER),
	new weTagDataOption('item'),
	), true, '');
$this->Attributes[] = new weTagData_textAttribute('level', false, '');
$this->Attributes[] = new weTagData_selectAttribute('current', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_choiceAttribute('position', array(new weTagDataOption('first'),
	new weTagDataOption('odd'),
	new weTagDataOption('even'),
	new weTagDataOption('last'),
	), false, false, '');
