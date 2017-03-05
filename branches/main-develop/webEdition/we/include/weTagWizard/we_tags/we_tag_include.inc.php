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

$included = new weTagData_selectAttribute('included', [], false, '');
$id = (defined('FILE_TABLE') ? new weTagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '') : null);
$path = (defined('FILE_TABLE') ? new weTagData_selectorAttribute('path', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '', true) : null);
//$path = new weTagData_textAttribute('path', false, '');
$gethttp = new weTagData_selectAttribute('gethttp', weTagData_selectAttribute::getTrueFalse(), false, '');
$seeMode = new weTagData_selectAttribute('seeMode', weTagData_selectAttribute::getTrueFalse(), false, '');
$once = new weTagData_selectAttribute('once', weTagData_selectAttribute::getTrueFalse(), false, '');
$kind = new weTagData_selectAttribute('kind', [new weTagDataOption('all', false, ''), new weTagDataOption('int', false, ''), new weTagDataOption('ext', false, '')], false, '');
$name = new weTagData_textAttribute('name', false, '');
$description = new weTagData_textAttribute('description', false, '');
$id_temp = (defined('TEMPLATES_TABLE') ? new weTagData_selectorAttribute('id', TEMPLATES_TABLE, 'text/weTmpl', false, '') : null);
$path_temp = (defined('TEMPLATES_TABLE') ? new weTagData_selectorAttribute('path', TEMPLATES_TABLE, 'text/weTmpl', false, '', true) : null);
$rootdir = new weTagData_textAttribute('rootdir', false, '');
$startid = new weTagData_selectorAttribute('startid', FILE_TABLE, weTagData_selectorAttribute::FOLDER, false, '');

$this->TypeAttribute = new weTagData_typeAttribute('type', [new weTagDataOption('document', false, '', [$id, $path, $gethttp, $seeMode, $kind, $name, $rootdir, $startid, $description], []),
	new weTagDataOption('template', false, '', [$path_temp, $id_temp, $once], [])], false, '');

$this->Attributes = [$included, $id, $path, $path_temp, $gethttp, $seeMode, $kind, $name, $id_temp, $once, $rootdir, $startid, $description];
