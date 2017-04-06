<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
*/
$this->NeedsEndTag = false;
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new we_tagData_textAttribute('name', true, '');
$this->Attributes[] = new we_tagData_textAttribute('width', false, '');
$this->Attributes[] = new we_tagData_textAttribute('height', false, '');
$this->Attributes[] = new we_tagData_selectAttribute('wmode', [new we_tagData_option('window'),
	new we_tagData_option('opaque'),
	new we_tagData_option('transparent'),
	], false, '');
$this->Attributes[] = new we_tagData_textAttribute('alt', false, '');
if(defined('FILE_TABLE')){
	$this->Attributes[] = new we_tagData_selectorAttribute('startid', FILE_TABLE, we_tagData_selectorAttribute::FOLDER, false, '');
	$this->Attributes[] = new we_tagData_selectorAttribute('parentid', FILE_TABLE, we_tagData_selectorAttribute::FOLDER, false, '');
}
$this->Attributes[] = new we_tagData_selectAttribute('showcontrol', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_selectAttribute('showflash', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_textAttribute('sizingrel', false, '');
$this->Attributes[] = new we_tagData_selectAttribute('sizingstyle', [new we_tagData_option('none'),
	new we_tagData_option('em'),
	new we_tagData_option('ex'),
	new we_tagData_option('%'),
	new we_tagData_option('px'),
	], false, '');
$this->Attributes[] = new we_tagData_textAttribute('sizingbase', false, '');
