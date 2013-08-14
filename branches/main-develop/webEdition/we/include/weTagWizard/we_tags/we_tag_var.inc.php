<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$name = new weTagData_textAttribute('name', true, '');
$doc = new weTagData_selectAttribute('doc', array(new weTagDataOption('self'),
	new weTagDataOption('top'),
	), false, '');
$win2iso = new weTagData_selectAttribute('win2iso', weTagData_selectAttribute::getTrueFalse(), false, '');
$htmlspecialchars = new weTagData_selectAttribute('htmlspecialchars', weTagData_selectAttribute::getTrueFalse(), false, '');
$key = new weTagData_selectAttribute('key', weTagData_selectAttribute::getTrueFalse(), false, '');

$this->TypeAttribute = new weTagData_typeAttribute('type', array(
	new weTagDataOption('document', false, '', array($name, $doc, $htmlspecialchars), array($name)),
	new weTagDataOption('property', false, '', array($name, $doc), array($name)),
	new weTagDataOption('global', false, '', array($name, $htmlspecialchars), array($name)),
	new weTagDataOption('img', false, '', array($name, $doc, $htmlspecialchars), array($name)),
	new weTagDataOption('href', false, '', array($name, $doc, $htmlspecialchars), array($name)),
	new weTagDataOption('date', false, '', array($name, $doc, $htmlspecialchars), array($name)),
	new weTagDataOption('link', false, '', array($name, $doc, $htmlspecialchars), array($name)),
	new weTagDataOption('multiobject', false, '', array($name, $doc), array($name)),
	new weTagDataOption('request', false, '', array($name, $htmlspecialchars), array($name)),
	new weTagDataOption('post', false, '', array($name, $htmlspecialchars), array($name)),
	new weTagDataOption('get', false, '', array($name, $htmlspecialchars), array($name)),
	new weTagDataOption('select', false, '', array($name, $doc, $htmlspecialchars, $key), array($name)),
	new weTagDataOption('session', false, '', array($name, $htmlspecialchars), array($name)),
	new weTagDataOption('shopVat', false, '', array($doc), array())), true, '');

$this->Attributes = array($name, $doc, $win2iso, $htmlspecialchars, $key);
