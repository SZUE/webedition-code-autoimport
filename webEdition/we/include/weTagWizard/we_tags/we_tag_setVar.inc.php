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
	new we_tagData_textAttribute('value', false, ''),
	new we_tagData_selectAttribute('from', [
		new we_tagData_option('request'),
		new we_tagData_option('post'),
		new we_tagData_option('get'),
		new we_tagData_option('global'),
		new we_tagData_option('session'),
		new we_tagData_option('top'),
		new we_tagData_option('self'),
		new we_tagData_option('object'),
		new we_tagData_option('document'),
		new we_tagData_option('sessionfield'),
		new we_tagData_option('calendar'),
		new we_tagData_option('listview'),
		new we_tagData_option('block'),
		new we_tagData_option('listdir'),
			], false, ''),
	new we_tagData_textAttribute('namefrom', false, ''),
	new we_tagData_selectAttribute('typefrom', [new we_tagData_option('text'),
		new we_tagData_option('date'),
		new we_tagData_option('img'),
		new we_tagData_option('flashmovie'),
		new we_tagData_option('href'),
		new we_tagData_option('link'),
		new we_tagData_option('select'),
		new we_tagData_option('binary'),
		new we_tagData_option('float'),
		new we_tagData_option('int'),
			], false, ''),
	new we_tagData_selectAttribute('propertyto', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_selectAttribute('propertyfrom', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_textAttribute('formnameto', false, ''),
	new we_tagData_textAttribute('formnamefrom', false, ''),
	new we_tagData_selectAttribute('varType', [new we_tagData_option(we_base_request::STRING),
		new we_tagData_option(we_base_request::INT),
		new we_tagData_option(we_base_request::BOOL),
		new we_tagData_option(we_base_request::FLOAT),
		new we_tagData_option(we_base_request::EMAIL),
		new we_tagData_option(we_base_request::URL),
		new we_tagData_option(we_base_request::RAW),
			], false, ''),
	new we_tagData_selectAttribute('prepareSQL', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_selectAttribute('striptags', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_textAttribute('outputlanguage', false, ''),
];
