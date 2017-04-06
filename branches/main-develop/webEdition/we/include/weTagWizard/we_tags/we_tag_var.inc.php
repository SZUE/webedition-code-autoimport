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

$name = new we_tagData_textAttribute('name', true, '');
$doc = new we_tagData_selectAttribute('doc', [new we_tagData_option('self'),
	new we_tagData_option('top'),
	], false, '');
$win2iso = new we_tagData_selectAttribute('win2iso', we_tagData_selectAttribute::getTrueFalse(), false, '');
$htmlspecialchars = new we_tagData_selectAttribute('htmlspecialchars', we_tagData_selectAttribute::getTrueFalse(), false, '');
$key = new we_tagData_selectAttribute('key', we_tagData_selectAttribute::getTrueFalse(), false, '');
$format = new we_tagData_textAttribute('format', false, '');
$num_format = new we_tagData_selectAttribute('num_format', [new we_tagData_option('german', false, ''), new we_tagData_option('english', false, ''), new we_tagData_option('french', false, ''), new we_tagData_option('swiss', false, '')], false, '');

$varType = new we_tagData_selectAttribute('varType', [new we_tagData_option(we_base_request::STRING),
	new we_tagData_option(we_base_request::INT),
	new we_tagData_option(we_base_request::BOOL),
	new we_tagData_option(we_base_request::FLOAT),
	new we_tagData_option(we_base_request::EMAIL),
	new we_tagData_option(we_base_request::URL),
	new we_tagData_option(we_base_request::RAW),
	], false, '');

$sql = new we_tagData_selectAttribute('prepareSQL', we_tagData_selectAttribute::getTrueFalse(), false, '');


$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('document', false, '', [$name, $doc, $htmlspecialchars, $format, $num_format, $sql], [$name]),
	new we_tagData_option('property', false, '', [$name, $doc, $format, $num_format, $sql], [$name]),
	new we_tagData_option('global', false, '', [$name, $htmlspecialchars, $format, $num_format, $sql], [$name]),
	new we_tagData_option('img', false, '', [$name, $doc, $htmlspecialchars, $sql], [$name]),
	new we_tagData_option('href', false, '', [$name, $doc, $htmlspecialchars, $sql], [$name]),
	new we_tagData_option('date', false, '', [$name, $doc, $htmlspecialchars, $format, $sql], [$name]),
	new we_tagData_option('link', false, '', [$name, $doc, $htmlspecialchars, $sql], [$name]),
	new we_tagData_option('multiobject', false, '', [$name, $doc, $sql], [$name]),
	new we_tagData_option('request', false, '', [$name, $varType, $htmlspecialchars, $format, $num_format, $sql], [$name]),
	new we_tagData_option('post', false, '', [$name, $varType, $htmlspecialchars, $format, $num_format, $sql], [$name]),
	new we_tagData_option('get', false, '', [$name, $varType, $htmlspecialchars, $format, $num_format, $sql], [$name]),
	new we_tagData_option('select', false, '', [$name, $doc, $htmlspecialchars, $key, $sql], [$name]),
	new we_tagData_option('session', false, '', [$name, $htmlspecialchars, $format, $num_format, $sql], [$name]),
	//new weTagDataOption('shopCategory', false, '', [$doc), []),
	new we_tagData_option('shopCategory', false, '', [], []),
	new we_tagData_option('shopVat', false, '', [$doc], [])
	], true, '');


$this->Attributes = [$name, $doc, $win2iso, $varType, $sql, $htmlspecialchars, $key];
