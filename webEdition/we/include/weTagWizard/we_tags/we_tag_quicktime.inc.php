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

$this->Attributes[] = new weTagData_textAttribute('name', true, '');
$this->Attributes[] = new weTagData_textAttribute('width', false, '');
$this->Attributes[] = new weTagData_textAttribute('height', false, '');
if(defined('FILE_TABLE')){
	$this->Attributes[] = new weTagData_selectorAttribute('startid', FILE_TABLE, weTagData_selectorAttribute::FOLDER, false, '');
	$this->Attributes[] = new weTagData_selectorAttribute('parentid', FILE_TABLE, weTagData_selectorAttribute::FOLDER, false, '');
}
$this->Attributes[] = new weTagData_selectAttribute('showcontrol', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('showquicktime', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_textAttribute('sizingrel', false, '');
$this->Attributes[] = new weTagData_selectAttribute('sizingstyle', array(new weTagDataOption('none'),
	new weTagDataOption('em'),
	new weTagDataOption('ex'),
	new weTagDataOption('%'),
	new weTagDataOption('px'),
	), false, '');
$this->Attributes[] = new weTagData_textAttribute('sizingbase', false, '');
