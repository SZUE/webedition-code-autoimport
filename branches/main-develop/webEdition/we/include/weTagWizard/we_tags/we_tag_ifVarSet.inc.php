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

$this->Attributes = [
	new weTagData_textAttribute('name', true, ''),
	new weTagData_selectAttribute('type', [new weTagDataOption('href'),
		new weTagDataOption('request'),
		new weTagDataOption('post'),
		new weTagDataOption('get'),
		new weTagDataOption('global'),
		new weTagDataOption('session'),
		new weTagDataOption('sessionfield'),
		new weTagDataOption('sum'),
		new weTagDataOption('shopField'),
		], false, ''),
	new weTagData_selectAttribute('doc', [new weTagDataOption('object'),
		new weTagDataOption('document'),
		new weTagDataOption('top'),
		], false, ''),
	new weTagData_selectAttribute('property', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_textAttribute('formname', false, ''),
	new weTagData_textAttribute('shopname', false, 'shop'),
];
