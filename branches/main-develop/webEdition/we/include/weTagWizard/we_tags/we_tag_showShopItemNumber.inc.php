<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes[] = new weTagData_textAttribute('shopname', true, '');
$this->Attributes[] = new weTagData_choiceAttribute('type', array(new weTagDataOption('select'),
	new weTagDataOption('textinput'),
	new weTagDataOption('print'),
	), false, false, '');
$this->Attributes[] = new weTagData_textAttribute('start', false, '');
$this->Attributes[] = new weTagData_textAttribute('stop', false, '');
$this->Attributes[] = new weTagData_selectAttribute('floatquantities', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_textAttribute('step', false, '1');
$this->Attributes[] = new weTagData_choiceAttribute('num_format', array(new weTagDataOption('german'),
	new weTagDataOption('french'),
	new weTagDataOption('english'),
	new weTagDataOption('swiss'),
	), false, false, '');
