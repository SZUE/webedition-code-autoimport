<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('index', false, '');
$this->Attributes[] = new weTagData_textAttribute('separator', false, '');
$this->Attributes[] = new weTagData_textAttribute('home', false, '');
$this->Attributes[] = new weTagData_selectAttribute('hidehome', array(new weTagDataOption('false'),
	new weTagDataOption('true'),
	), false, '');
$this->Attributes[] = new weTagData_textAttribute('field', false, '');
$this->Attributes[] = new weTagData_textAttribute('dirfield', false, '');
$this->Attributes[] = new weTagData_selectAttribute('fieldforfolder', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('top'),
	new weTagDataOption('self'),
	), false, '');
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

//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');
