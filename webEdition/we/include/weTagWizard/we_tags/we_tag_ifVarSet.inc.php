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
	new we_tagData_textAttribute('name', true, ''),
	new we_tagData_selectAttribute('type', [new we_tagData_option('href'),
		new we_tagData_option('request'),
		new we_tagData_option('post'),
		new we_tagData_option('get'),
		new we_tagData_option('global'),
		new we_tagData_option('session'),
		new we_tagData_option('sessionfield'),
		new we_tagData_option('sum'),
		new we_tagData_option('shopField'),
		], false, ''),
	new we_tagData_selectAttribute('doc', [new we_tagData_option('object'),
		new we_tagData_option('document'),
		new we_tagData_option('top'),
		], false, ''),
	new we_tagData_selectAttribute('property', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_textAttribute('formname', false, ''),
	new we_tagData_textAttribute('shopname', false, 'shop'),
];
