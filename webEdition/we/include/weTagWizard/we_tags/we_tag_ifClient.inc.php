<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_choiceAttribute('browser', array(
	new weTagDataOption('ie'),
	new weTagDataOption('nn'),
	new weTagDataOption('mozilla'),
	new weTagDataOption('safari'),
	new weTagDataOption('opera'),
	new weTagDataOption('lynx'),
	new weTagDataOption('konqueror'),
	new weTagDataOption('firefox'),
	new weTagDataOption('chrome'),
	new weTagDataOption('unknown'),
	), false, false, '');
$this->Attributes[] = new weTagData_selectAttribute('operator', array(
	new weTagDataOption('equal'),
	new weTagDataOption('less'),
	new weTagDataOption('less|equal'),
	new weTagDataOption('greater'),
	new weTagDataOption('greater|equal'),
	new weTagDataOption('every'),
	), false, '');
$this->Attributes[] = new weTagData_textAttribute('version', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('system', array(
	new weTagDataOption('win'),
	new weTagDataOption('mac'),
	new weTagDataOption('unix'),
	new weTagDataOption('android'),
	new weTagDataOption('iphone'),
	new weTagDataOption('unknown'),
	), false, false, '');
