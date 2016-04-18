<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('name', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('only', array(
	new weTagDataOption('width', false, ''),
	new weTagDataOption('height', false, ''),
	new weTagDataOption('alt', false, ''),
	new weTagDataOption('title', false, ''),
	new weTagDataOption('src', false, ''),
	new weTagDataOption('id', false, ''),
	new weTagDataOption('path', false, ''),
	), false, true, '');
if(defined('FILE_TABLE')){
	$this->Attributes[] = new weTagData_selectorAttribute('id', FILE_TABLE, 'image/*', false, '');
}
$this->Attributes[] = new weTagData_textAttribute('width', false, '');
$this->Attributes[] = new weTagData_textAttribute('height', false, '');
$this->Attributes[] = new weTagData_textAttribute('border', false, '');
$this->Attributes[] = new weTagData_textAttribute('hspace', false, '');
$this->Attributes[] = new weTagData_textAttribute('vspace', false, '');
$this->Attributes[] = new weTagData_textAttribute('alt', false, '');
$this->Attributes[] = new weTagData_textAttribute('title', false, '');
if(defined('FILE_TABLE')){
	$this->Attributes[] = new weTagData_selectorAttribute('startid', FILE_TABLE, weTagData_selectorAttribute::FOLDER, false, '');
}
if(defined('FILE_TABLE')){
	$this->Attributes[] = new weTagData_selectorAttribute('parentid', FILE_TABLE, weTagData_selectorAttribute::FOLDER, false, '');
}
$this->Attributes[] = new weTagData_selectAttribute('align', array(new weTagDataOption('left', false, ''), new weTagDataOption('right', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('bottom', false, ''), new weTagDataOption('absmiddle', false, ''), new weTagDataOption('middle', false, ''), new weTagDataOption('texttop', false, ''), new weTagDataOption('baseline', false, ''), new weTagDataOption('absbottom', false, '')), false, '');
$this->Attributes[] = new weTagData_sqlRowAttribute('thumbnail', THUMBNAILS_TABLE, false, 'Name', '', '', '');
$this->Attributes[] = new weTagData_selectAttribute('showcontrol', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('showimage', array(new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('showinputs', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('showthumbcontrol', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_textAttribute('sizingrel', false, '');
$this->Attributes[] = new weTagData_selectAttribute('sizingstyle', array(new weTagDataOption('none', false, ''), new weTagDataOption('em', false, ''), new weTagDataOption('ex', false, ''), new weTagDataOption('%', false, ''), new weTagDataOption('px', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('sizingbase', false, '');
