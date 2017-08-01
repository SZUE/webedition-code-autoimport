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

$included = new we_tagData_selectAttribute('included', [], false, '');
$id = (defined('FILE_TABLE') ? new we_tagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '') : null);
$path = (defined('FILE_TABLE') ? new we_tagData_selectorAttribute('path', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '', true) : null);
//$path = new weTagData_textAttribute('path', false, '');
$gethttp = new we_tagData_selectAttribute('gethttp', we_tagData_selectAttribute::getTrueFalse(), false, '');
$seeMode = new we_tagData_selectAttribute('seeMode', we_tagData_selectAttribute::getTrueFalse(), false, '');
$once = new we_tagData_selectAttribute('once', we_tagData_selectAttribute::getTrueFalse(), false, '');
$kind = new we_tagData_selectAttribute('kind', [new we_tagData_option('all', false, ''), new we_tagData_option('int', false, ''), new we_tagData_option('ext', false, '')], false, '');
$name = new we_tagData_textAttribute('name', false, '');
$description = new we_tagData_textAttribute('description', false, '');
$id_temp = (defined('TEMPLATES_TABLE') ? new we_tagData_selectorAttribute('id', TEMPLATES_TABLE, we_base_ContentTypes::TEMPLATE, false, '') : null);
$path_temp = (defined('TEMPLATES_TABLE') ? new we_tagData_selectorAttribute('path', TEMPLATES_TABLE, we_base_ContentTypes::TEMPLATE, false, '', true) : null);
$rootdir = new we_tagData_textAttribute('rootdir', false, '');
$startid = new we_tagData_selectorAttribute('startid', FILE_TABLE, we_tagData_selectorAttribute::FOLDER, false, '');

$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('document', false, '', [$id, $path, $gethttp, $seeMode, $kind, $name, $rootdir, $startid, $description], []),
	new we_tagData_option('template', false, '', [$path_temp, $id_temp, $once], [])], false, '');

$this->Attributes = [$included, $id, $path, $path_temp, $gethttp, $seeMode, $kind, $name, $id_temp, $once, $rootdir, $startid, $description];
