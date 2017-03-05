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
	new weTagData_textAttribute('id', false, ''),
	new weTagData_selectAttribute('allowredirect', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_selectAttribute('writeto', [new weTagDataOption('voting'),
		new weTagDataOption('session'),
	 ], false, ''),
	new weTagData_selectAttribute('deletesessiondata', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_textAttribute('additionalfields', false, ''),
];
