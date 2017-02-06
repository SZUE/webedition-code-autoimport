<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_choiceAttribute('type', array(new weTagDataOption('button'),
	new weTagDataOption('checkbox'),
	), true, false, '');
$this->Attributes[] = new weTagData_choiceAttribute('name', array(new weTagDataOption('delete'),
	new weTagDataOption('makeSameDoc'),
	new weTagDataOption('publish'),
	new weTagDataOption('save'),
	new weTagDataOption('unpublish'),
	new weTagDataOption('workflow', false, 'workflow')), true, false, '');
$this->Attributes[] = new weTagData_choiceAttribute('hide', weTagData_selectAttribute::getTrueFalse(), false, false, '');
$this->Attributes[] = new weTagData_choiceAttribute('readonly', weTagData_selectAttribute::getTrueFalse(), false, false, '');
$this->Attributes[] = new weTagData_choiceAttribute('checked', weTagData_selectAttribute::getTrueFalse(), false, false, '');
