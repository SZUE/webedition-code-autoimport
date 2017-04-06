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

$id_document = new we_tagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT . ',image/*,text/css,text/js,application/*', true, '');
$id_object = (defined('OBJECT_FILES_TABLE') ? new we_tagData_selectorAttribute('id', OBJECT_FILES_TABLE, 'objectFile', true, '') : null);
$triggerid = new we_tagData_selectorAttribute('triggerid', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$hidedirindex = new we_tagData_selectAttribute('hidedirindex', we_tagData_selectAttribute::getTrueFalse(), false, '');
$objectseourls = new we_tagData_selectAttribute('objectseourls', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes = [];
$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('document', false, '', [$id_document, $hidedirindex], [$id_document]),
	new we_tagData_option('object', false, 'object', [$id_object, $triggerid, $hidedirindex, $objectseourls], [$id_object])], false, '');

$this->Attributes = [$id_document, $id_object, $triggerid, $hidedirindex, $objectseourls];
