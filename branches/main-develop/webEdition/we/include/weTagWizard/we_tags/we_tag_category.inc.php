<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('delimiter', false, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', [new weTagDataOption('self'),
	new weTagDataOption('top'),
	new weTagDataOption('listview'),
	], false, '');
$this->Attributes[] = new weTagData_selectAttribute('showpath', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_textAttribute('rootdir', false, '');
$this->Attributes[] = new weTagData_selectAttribute('field', [new weTagDataOption('ID'),
	new weTagDataOption('Path'),
	new weTagDataOption('Title'),
	new weTagDataOption('Description'),
	], false, '');
$this->Attributes[] = new weTagData_textAttribute('onlyindir', false, '');
$this->Attributes[] = (defined('CATEGORY_TABLE') ? new weTagData_selectorAttribute('id', CATEGORY_TABLE, '', false, '') : null);
$this->Attributes[] = new weTagData_textAttribute('separator', false, '');
$this->Attributes[] = new weTagData_textAttribute('name', false, '');
$this->Attributes[] = new weTagData_selectAttribute('multiple', weTagData_selectAttribute::getTrueFalse(), false, '');
