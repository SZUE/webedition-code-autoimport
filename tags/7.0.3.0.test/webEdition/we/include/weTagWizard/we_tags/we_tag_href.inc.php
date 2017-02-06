
<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = array(
	new weTagData_textAttribute('name', true, ''),
	new weTagData_textAttribute('size', false, ''),
	new weTagData_selectAttribute('type', array(new weTagDataOption('all'),
		new weTagDataOption('int'),
		new weTagDataOption('ext'),
		), false, ''),
	new weTagData_selectAttribute('include', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_selectAttribute('file', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_selectAttribute('directory', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_selectAttribute('reload', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_selectAttribute('hidedirindex', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_textAttribute('user', false, 'users'),
	new weTagData_textAttribute('rootdir', false, ''),
	new weTagData_selectorAttribute('startid', FILE_TABLE, weTagData_selectorAttribute::FOLDER, false, ''),
	new weTagData_selectAttribute('cfilter', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_selectAttribute('only', array(new weTagDataOption('id'), new weTagDataOption('path')), false, ''),
);
