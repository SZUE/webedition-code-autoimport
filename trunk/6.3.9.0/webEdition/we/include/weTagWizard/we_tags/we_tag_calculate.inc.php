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
$this->Attributes[] = new weTagData_textAttribute('decimals', true, '');
