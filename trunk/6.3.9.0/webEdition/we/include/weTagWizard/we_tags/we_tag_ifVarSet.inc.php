<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('name', true, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('href'),
	new weTagDataOption('request'),
	new weTagDataOption('post'),
	new weTagDataOption('get'),
	new weTagDataOption('global'),
	new weTagDataOption('session'),
	new weTagDataOption('sessionfield'),
	new weTagDataOption('sum'),
	new weTagDataOption('shopField'),
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('object'),
	new weTagDataOption('document'),
	new weTagDataOption('top'),
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('property', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_textAttribute('formname', false, '');
$this->Attributes[] = new weTagData_textAttribute('shopname', false, 'shop');
