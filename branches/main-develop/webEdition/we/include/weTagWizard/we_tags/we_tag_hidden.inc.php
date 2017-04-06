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
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new we_tagData_textAttribute('name', true, ''),
	new we_tagData_selectAttribute('type', [new we_tagData_option('global'),
		new we_tagData_option('request'),
		new we_tagData_option('session'),
		], false, ''),
	new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, ''),
];
