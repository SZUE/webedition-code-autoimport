<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$name = new weTagData_textAttribute('name', true, '');
$doc = new weTagData_selectAttribute('doc', array(
	new weTagDataOption('self'),
	new weTagDataOption('top'),
	), false, '');
$win2iso = new weTagData_selectAttribute('win2iso', weTagData_selectAttribute::getTrueFalse(), false, '');
$htmlspecialchars = new weTagData_selectAttribute('htmlspecialchars', weTagData_selectAttribute::getTrueFalse(), false, '');
$key = new weTagData_selectAttribute('key', weTagData_selectAttribute::getTrueFalse(), false, '');
$format = new weTagData_textAttribute('format', false, '');
$num_format = new weTagData_selectAttribute('num_format', array(new weTagDataOption('german', false, ''), new weTagDataOption('english', false, ''), new weTagDataOption('french', false, ''), new weTagDataOption('swiss', false, '')), false, '');

$varType = new weTagData_selectAttribute('varType', array(
	new weTagDataOption(we_base_request::STRING),
	new weTagDataOption(we_base_request::INT),
	new weTagDataOption(we_base_request::BOOL),
	new weTagDataOption(we_base_request::FLOAT),
	new weTagDataOption(we_base_request::EMAIL),
	new weTagDataOption(we_base_request::URL),
	new weTagDataOption(we_base_request::RAW),
	), false, '');

$sql = new weTagData_selectAttribute('prepareSQL', weTagData_selectAttribute::getTrueFalse(), false, '');


$this->TypeAttribute = new weTagData_typeAttribute('type', array(
	new weTagDataOption('document', false, '', array($name, $doc, $htmlspecialchars, $format, $num_format, $sql), array($name)),
	new weTagDataOption('property', false, '', array($name, $doc, $format, $num_format, $sql), array($name)),
	new weTagDataOption('global', false, '', array($name, $htmlspecialchars, $format, $num_format, $sql), array($name)),
	new weTagDataOption('img', false, '', array($name, $doc, $htmlspecialchars, $sql), array($name)),
	new weTagDataOption('href', false, '', array($name, $doc, $htmlspecialchars, $sql), array($name)),
	new weTagDataOption('date', false, '', array($name, $doc, $htmlspecialchars, $format, $sql), array($name)),
	new weTagDataOption('link', false, '', array($name, $doc, $htmlspecialchars, $sql), array($name)),
	new weTagDataOption('multiobject', false, '', array($name, $doc, $sql), array($name)),
	new weTagDataOption('request', false, '', array($name, $varType, $htmlspecialchars, $format, $num_format, $sql), array($name)),
	new weTagDataOption('post', false, '', array($name, $varType, $htmlspecialchars, $format, $num_format, $sql), array($name)),
	new weTagDataOption('get', false, '', array($name, $varType, $htmlspecialchars, $format, $num_format, $sql), array($name)),
	new weTagDataOption('select', false, '', array($name, $doc, $htmlspecialchars, $key, $sql), array($name)),
	new weTagDataOption('session', false, '', array($name, $htmlspecialchars, $format, $num_format, $sql), array($name)),
	//new weTagDataOption('shopCategory', false, '', array($doc), array()),
	new weTagDataOption('shopCategory', false, '', array(), array()),
	new weTagDataOption('shopVat', false, '', array($doc), array())
	), true, '');


$this->Attributes = array($name, $doc, $win2iso, $varType, $sql, $htmlspecialchars, $key);
