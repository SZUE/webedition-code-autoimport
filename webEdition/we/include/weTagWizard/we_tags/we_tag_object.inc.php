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
$this->Groups[] = 'input_tags';
$this->Module = 'object';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new we_tagData_textAttribute('name', false, '');
$this->Attributes[] = new we_tagData_textAttribute('text', false, '');
if(defined('OBJECT_TABLE')){
	$this->Attributes[] = new we_tagData_selectorAttribute('classid', OBJECT_TABLE, 'object', false, '');
}
if(defined('OBJECT_FILES_TABLE')){
	$this->Attributes[] = new we_tagData_selectorAttribute('id', OBJECT_FILES_TABLE, 'objectFile', false, '');
}
$this->Attributes[] = new we_tagData_textAttribute('size', false, '');
if(defined('FILE_TABLE')){
	$this->Attributes[] = new we_tagData_selectorAttribute('triggerid', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
}
$this->Attributes[] = new we_tagData_selectAttribute('hidedirindex', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_selectAttribute('objectseourls', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_selectAttribute('searchable', we_tagData_selectAttribute::getTrueFalse(), false, '');
