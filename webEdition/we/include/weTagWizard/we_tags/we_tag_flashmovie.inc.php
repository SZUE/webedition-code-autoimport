<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('name', true, '');
$this->Attributes[] = new weTagData_textAttribute('width', false, '');
$this->Attributes[] = new weTagData_textAttribute('height', false, '');
$this->Attributes[] = new weTagData_selectAttribute('wmode', [new weTagDataOption('window'),
	new weTagDataOption('opaque'),
	new weTagDataOption('transparent'),
	], false, '');
$this->Attributes[] = new weTagData_textAttribute('alt', false, '');
if(defined('FILE_TABLE')){
	$this->Attributes[] = new weTagData_selectorAttribute('startid', FILE_TABLE, weTagData_selectorAttribute::FOLDER, false, '');
	$this->Attributes[] = new weTagData_selectorAttribute('parentid', FILE_TABLE, weTagData_selectorAttribute::FOLDER, false, '');
}
$this->Attributes[] = new weTagData_selectAttribute('showcontrol', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('showflash', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_textAttribute('sizingrel', false, '');
$this->Attributes[] = new weTagData_selectAttribute('sizingstyle', [new weTagDataOption('none'),
	new weTagDataOption('em'),
	new weTagDataOption('ex'),
	new weTagDataOption('%'),
	new weTagDataOption('px'),
	], false, '');
$this->Attributes[] = new weTagData_textAttribute('sizingbase', false, '');
