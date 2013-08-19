<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_choiceAttribute('type', array(new weTagDataOption('button', false, ''), new weTagDataOption('checkbox', false, '')), true,false, '');
$this->Attributes[] = new weTagData_choiceAttribute('name', array(new weTagDataOption('delete', false, ''), new weTagDataOption('makeSameDoc', false, ''), new weTagDataOption('publish', false, ''), new weTagDataOption('save', false, ''), new weTagDataOption('unpublish', false, ''), new weTagDataOption('workflow', false, 'workflow')), true,false, '');
$this->Attributes[] = new weTagData_choiceAttribute('hide', weTagData_selectAttribute::getTrueFalse(), false,false, '');
$this->Attributes[] = new weTagData_choiceAttribute('readonly', weTagData_selectAttribute::getTrueFalse(), false,false, '');
$this->Attributes[] = new weTagData_choiceAttribute('checked', weTagData_selectAttribute::getTrueFalse(), false,false, '');
