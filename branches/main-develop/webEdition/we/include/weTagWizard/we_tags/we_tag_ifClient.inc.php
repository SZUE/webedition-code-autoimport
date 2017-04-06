<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
*/
$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new we_tagData_choiceAttribute('browser', [new we_tagData_option(we_base_browserDetect::IE),
	new we_tagData_option(we_base_browserDetect::EDGE),
	new we_tagData_option(we_base_browserDetect::NETSCAPE),
	new we_tagData_option(we_base_browserDetect::MOZILLA),
	new we_tagData_option(we_base_browserDetect::SAFARI),
	new we_tagData_option(we_base_browserDetect::OPERA),
	new we_tagData_option(we_base_browserDetect::LYNX),
	new we_tagData_option(we_base_browserDetect::KONQUEROR),
	new we_tagData_option(we_base_browserDetect::FF),
	new we_tagData_option(we_base_browserDetect::CHROME),
	new we_tagData_option(we_base_browserDetect::UNKNOWN),
	], false, false, '');
$this->Attributes[] = new we_tagData_selectAttribute('operator', [new we_tagData_option('equal'),
	new we_tagData_option('less'),
	new we_tagData_option('less|equal'),
	new we_tagData_option('greater'),
	new we_tagData_option('greater|equal'),
	new we_tagData_option('every'),
	], false, '');
$this->Attributes[] = new we_tagData_textAttribute('version', false, '');
$this->Attributes[] = new we_tagData_choiceAttribute('system', [new we_tagData_option(we_base_browserDetect::SYS_WIN),
	new we_tagData_option(we_base_browserDetect::SYS_MAC),
	new we_tagData_option(we_base_browserDetect::SYS_UNIX),
	new we_tagData_option(we_base_browserDetect::SYS_ANDROID),
	new we_tagData_option(we_base_browserDetect::SYS_IPHONE),
	new we_tagData_option(we_base_browserDetect::UNKNOWN),
	], false, false, '');
