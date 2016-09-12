<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$name = new weTagData_textAttribute('name', true, '');
$doc = new weTagData_selectAttribute('doc', [new weTagDataOption('self'),
	new weTagDataOption('top'),
	], false, '');
$win2iso = new weTagData_selectAttribute('win2iso', weTagData_selectAttribute::getTrueFalse(), false, '');
$htmlspecialchars = new weTagData_selectAttribute('htmlspecialchars', weTagData_selectAttribute::getTrueFalse(), false, '');
$key = new weTagData_selectAttribute('key', weTagData_selectAttribute::getTrueFalse(), false, '');
$format = new weTagData_textAttribute('format', false, '');
$num_format = new weTagData_selectAttribute('num_format', array(new weTagDataOption('german', false, ''), new weTagDataOption('english', false, ''), new weTagDataOption('french', false, ''), new weTagDataOption('swiss', false, '')), false, '');

$varType = new weTagData_selectAttribute('varType', [new weTagDataOption(we_base_request::STRING),
	new weTagDataOption(we_base_request::INT),
	new weTagDataOption(we_base_request::BOOL),
	new weTagDataOption(we_base_request::FLOAT),
	new weTagDataOption(we_base_request::EMAIL),
	new weTagDataOption(we_base_request::URL),
	new weTagDataOption(we_base_request::RAW),
	], false, '');

$sql = new weTagData_selectAttribute('prepareSQL', weTagData_selectAttribute::getTrueFalse(), false, '');


$this->TypeAttribute = new weTagData_typeAttribute('type', [new weTagDataOption('document', false, '', [$name, $doc, $htmlspecialchars, $format, $num_format, $sql], [$name]),
	new weTagDataOption('property', false, '', [$name, $doc, $format, $num_format, $sql], [$name]),
	new weTagDataOption('global', false, '', [$name, $htmlspecialchars, $format, $num_format, $sql], [$name]),
	new weTagDataOption('img', false, '', [$name, $doc, $htmlspecialchars, $sql], [$name]),
	new weTagDataOption('href', false, '', [$name, $doc, $htmlspecialchars, $sql], [$name]),
	new weTagDataOption('date', false, '', [$name, $doc, $htmlspecialchars, $format, $sql], [$name]),
	new weTagDataOption('link', false, '', [$name, $doc, $htmlspecialchars, $sql], [$name]),
	new weTagDataOption('multiobject', false, '', [$name, $doc, $sql], [$name]),
	new weTagDataOption('request', false, '', [$name, $varType, $htmlspecialchars, $format, $num_format, $sql], [$name]),
	new weTagDataOption('post', false, '', [$name, $varType, $htmlspecialchars, $format, $num_format, $sql], [$name]),
	new weTagDataOption('get', false, '', [$name, $varType, $htmlspecialchars, $format, $num_format, $sql], [$name]),
	new weTagDataOption('select', false, '', [$name, $doc, $htmlspecialchars, $key, $sql], [$name]),
	new weTagDataOption('session', false, '', [$name, $htmlspecialchars, $format, $num_format, $sql], [$name]),
	//new weTagDataOption('shopCategory', false, '', array($doc), []),
	new weTagDataOption('shopCategory', false, '', [], []),
	new weTagDataOption('shopVat', false, '', [$doc], [])
	], true, '');


$this->Attributes = [$name, $doc, $win2iso, $varType, $sql, $htmlspecialchars, $key];
