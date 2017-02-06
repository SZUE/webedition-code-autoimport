<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_choiceAttribute('browser', array(
	new weTagDataOption(we_base_browserDetect::IE),
	new weTagDataOption(we_base_browserDetect::EDGE),
	new weTagDataOption(we_base_browserDetect::NETSCAPE),
	new weTagDataOption(we_base_browserDetect::MOZILLA),
	new weTagDataOption(we_base_browserDetect::SAFARI),
	new weTagDataOption(we_base_browserDetect::OPERA),
	new weTagDataOption(we_base_browserDetect::LYNX),
	new weTagDataOption(we_base_browserDetect::KONQUEROR),
	new weTagDataOption(we_base_browserDetect::FF),
	new weTagDataOption(we_base_browserDetect::CHROME),
	new weTagDataOption(we_base_browserDetect::UNKNOWN),
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
	new weTagDataOption(we_base_browserDetect::SYS_WIN),
	new weTagDataOption(we_base_browserDetect::SYS_MAC),
	new weTagDataOption(we_base_browserDetect::SYS_UNIX),
	new weTagDataOption(we_base_browserDetect::SYS_ANDROID),
	new weTagDataOption(we_base_browserDetect::SYS_IPHONE),
	new weTagDataOption(we_base_browserDetect::UNKNOWN),
	), false, false, '');
