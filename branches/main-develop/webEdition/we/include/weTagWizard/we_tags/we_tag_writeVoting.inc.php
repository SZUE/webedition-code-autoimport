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
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'voting';

$this->Attributes = [
	new we_tagData_textAttribute('id', false, ''),
	new we_tagData_selectAttribute('allowredirect', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_selectAttribute('writeto', [new we_tagData_option('voting'),
		new we_tagData_option('session'),
	 ], false, ''),
	new we_tagData_selectAttribute('deletesessiondata', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_textAttribute('additionalfields', false, ''),
];
