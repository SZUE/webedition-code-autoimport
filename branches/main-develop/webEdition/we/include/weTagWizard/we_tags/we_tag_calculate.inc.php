<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes[] = new weTagData_textAttribute('sum', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('num_format', array(new weTagDataOption('german'),
	new weTagDataOption('french'),
	new weTagDataOption('english'),
	new weTagDataOption('swiss'),
	), false, false, '');
$this->Attributes[] = new weTagData_choiceAttribute('print', weTagData_selectAttribute::getTrueFalse(), false, false, '');
$this->Attributes[] = new weTagData_selectAttribute('to', array(new weTagDataOption('screen'),
	new weTagDataOption('request'),
	new weTagDataOption('post'),
	new weTagDataOption('get'),
	new weTagDataOption('global'),
	new weTagDataOption('session'),
	new weTagDataOption('top'),
	new weTagDataOption('self'),
	new weTagDataOption('sessionfield'),
	), false, '');
$this->Attributes[] = new weTagData_textAttribute('nameto', false, '');