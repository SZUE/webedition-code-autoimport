<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'if_tags';
$this->Module = 'object';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('complete'),
	new weTagDataOption('language'),
	new weTagDataOption('country'),
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('case', array(new weTagDataOption('unchanged'),
	new weTagDataOption('uppercase'),
	new weTagDataOption('lowercase'),
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
