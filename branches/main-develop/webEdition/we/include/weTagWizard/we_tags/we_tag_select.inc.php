<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->DefaultValue = '<option>#1</option>
<option>#2</option>
<option>#3</option>';

$this->Attributes[] = new weTagData_textAttribute('name', true, '');
$this->Attributes[] = new weTagData_textAttribute('size', false, '');
$this->Attributes[] = new weTagData_selectAttribute('reload', weTagData_selectAttribute::getTrueFalse(), false, '');
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