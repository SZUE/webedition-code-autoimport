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

$id_document = new weTagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT . ',image/*,text/css,text/js,application/*', true, '');
$id_object = (defined('OBJECT_FILES_TABLE') ? new weTagData_selectorAttribute('id', OBJECT_FILES_TABLE, 'objectFile', true, '') : null);
$triggerid = new weTagData_selectorAttribute('triggerid', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$hidedirindex = new weTagData_selectAttribute('hidedirindex', weTagData_selectAttribute::getTrueFalse(), false, '');
$objectseourls = new weTagData_selectAttribute('objectseourls', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes = [];
$this->TypeAttribute = new weTagData_typeAttribute('type', [new weTagDataOption('document', false, '', [$id_document, $hidedirindex], [$id_document]),
	new weTagDataOption('object', false, 'object', [$id_object, $triggerid, $hidedirindex, $objectseourls], [$id_object])], false, '');

$this->Attributes = [$id_document, $id_object, $triggerid, $hidedirindex, $objectseourls];
