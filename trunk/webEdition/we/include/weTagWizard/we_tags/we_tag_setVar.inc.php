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

$this->Attributes = array(
	new weTagData_textAttribute('value', false, ''),
	new weTagData_selectAttribute('from', array(
		new weTagDataOption('request'),
		new weTagDataOption('post'),
		new weTagDataOption('get'),
		new weTagDataOption('global'),
		new weTagDataOption('session'),
		new weTagDataOption('top'),
		new weTagDataOption('self'),
		new weTagDataOption('object'),
		new weTagDataOption('document'),
		new weTagDataOption('sessionfield'),
		new weTagDataOption('calendar'),
		new weTagDataOption('listview'),
		new weTagDataOption('block'),
		new weTagDataOption('listdir'),
			), false, ''),
	new weTagData_textAttribute('namefrom', false, ''),
	new weTagData_selectAttribute('typefrom', array(
		new weTagDataOption('text'),
		new weTagDataOption('date'),
		new weTagDataOption('img'),
		new weTagDataOption('flashmovie'),
		new weTagDataOption('href'),
		new weTagDataOption('link'),
		new weTagDataOption('select'),
		new weTagDataOption('binary'),
		new weTagDataOption('float'),
		new weTagDataOption('int'),
			), false, ''),

	new weTagData_selectAttribute('propertyto', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_selectAttribute('propertyfrom', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_textAttribute('formnameto', false, ''),
	new weTagData_textAttribute('formnamefrom', false, ''),
	new weTagData_selectAttribute('varType', array(
	new weTagDataOption(we_base_request::STRING),
	new weTagDataOption(we_base_request::INT),
	new weTagDataOption(we_base_request::BOOL),
	new weTagDataOption(we_base_request::FLOAT),
	new weTagDataOption(we_base_request::EMAIL),
	new weTagDataOption(we_base_request::URL),
	new weTagDataOption(we_base_request::RAW),
		), false, ''),
	new weTagData_selectAttribute('prepareSQL', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_selectAttribute('striptags', weTagData_selectAttribute::getTrueFalse(), false, ''),
);
