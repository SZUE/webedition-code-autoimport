<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_selectAttribute('to', array(new weTagDataOption('request'),
	new weTagDataOption('post'),
	new weTagDataOption('get'),
	new weTagDataOption('global'),
	new weTagDataOption('session'),
	new weTagDataOption('top'),
	new weTagDataOption('self'),
	new weTagDataOption('object'),
	new weTagDataOption('document'),
	new weTagDataOption('sessionfield'),
	), true, '');
$this->Attributes[] = new weTagData_textAttribute('nameto', true, '');
$this->Attributes[] = new weTagData_textAttribute('value', false, '');
$this->Attributes[] = new weTagData_selectAttribute('from', array(new weTagDataOption('request'),
	new weTagDataOption('post'),
	new weTagDataOption('get'),
	new weTagDataOption('global'),
	new weTagDataOption('session'),
	new weTagDataOption('top'),
	new weTagDataOption('self'),
	new weTagDataOption('object'),
	new weTagDataOption('document'),
	new weTagDataOption('sessionfield'),
	new weTagDataOption('calendar'),
	new weTagDataOption('listview'),
	new weTagDataOption('block'),
	new weTagDataOption('listdir'),
	), false, '');
$this->Attributes[] = new weTagData_textAttribute('namefrom', false, '');
$this->Attributes[] = new weTagData_selectAttribute('typefrom', array(new weTagDataOption('text'),
	new weTagDataOption('date'),
	new weTagDataOption('img'),
	new weTagDataOption('flashmovie'),
	new weTagDataOption('href'),
	new weTagDataOption('link'),
	new weTagDataOption('select'),
	new weTagDataOption('binary'),
	new weTagDataOption('float'),
	new weTagDataOption('int'),
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('propertyto', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('propertyfrom', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_textAttribute('formnameto', false, '');
$this->Attributes[] = new weTagData_textAttribute('formnamefrom', false, '');
$this->Attributes[] = new weTagData_selectAttribute('striptags', weTagData_selectAttribute::getTrueFalse(), false, '');
