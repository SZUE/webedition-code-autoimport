<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$name = new weTagData_textAttribute('name', true, '');
$size = new weTagData_textAttribute('size', false, '');
$maxlength = new weTagData_textAttribute('maxlength', false, '');
$format = new weTagData_textAttribute('format', false, '');
$mode = new weTagData_selectAttribute('mode', array(new weTagDataOption('add', false, ''), new weTagDataOption('replace', false, '')), false, '');
$value = new weTagData_textAttribute('value', false, '');
$values = new weTagData_textAttribute('values', false, '');
$html = new weTagData_selectAttribute('html', weTagData_selectAttribute::getTrueFalse(), false, '');
$htmlspecialchars = new weTagData_selectAttribute('htmlspecialchars', weTagData_selectAttribute::getTrueFalse(), false, '');
$php = new weTagData_selectAttribute('php', weTagData_selectAttribute::getTrueFalse(), false, '');
$num_format = new weTagData_selectAttribute('num_format', array(new weTagDataOption('german', false, ''), new weTagDataOption('english', false, ''), new weTagDataOption('french', false, ''), new weTagDataOption('swiss', false, '')), false, '');
$precision = new weTagData_textAttribute('precision', false, '');
$win2iso = new weTagData_selectAttribute('win2iso', weTagData_selectAttribute::getTrueFalse(), false, '');
$reload = new weTagData_selectAttribute('reload', weTagData_selectAttribute::getTrueFalse(), false, '');
$seperator = new weTagData_textAttribute('seperator', false, '');
$user = new weTagData_multiSelectorAttribute('user', USER_TABLE, 'user,folder', 'Text', false, 'users');
//$spellcheck = new weTagData_selectAttribute('spellcheck', weTagData_selectAttribute::getTrueFalse(), false, 'spellchecker');
$outputlanguage = new weTagData_textAttribute('outputlanguage', false, '');
$doc = new weTagData_selectAttribute('doc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')), false, '');
$currentdate = new weTagData_selectAttribute('currentdate', weTagData_selectAttribute::getTrueFalse(), false, '');

$this->TypeAttribute = new weTagData_typeAttribute('type', array(
	new weTagDataOption('text', false, '', array($name, $size, $maxlength, $value, $html, $php, $num_format, $precision, $user, $htmlspecialchars/*, $spellcheck,*/), array($name)),
	new weTagDataOption('checkbox', false, '', array($name, $value, $reload, $user, $htmlspecialchars,), array($name)),
	new weTagDataOption('date', false, '', array($name, $format, $currentdate, $user, $htmlspecialchars,), array($name)),
	new weTagDataOption('choice', false, '', array($name, $size, $maxlength, $mode, $values, $reload, $seperator, $user, $htmlspecialchars,), array($name)),
	new weTagDataOption('select', false, '', array($name, $values, $htmlspecialchars,), array($name)),
	new weTagDataOption('country', false, '', array($name, $outputlanguage, $doc,), array($name)),
	new weTagDataOption('language', false, '', array($name, $outputlanguage, $doc), array($name))), true, '');

$this->Attributes = array($name, $size, $maxlength, $format, $mode, $value, $values, $html, $htmlspecialchars, $php, $num_format, $precision, $win2iso, $reload,
	$seperator, $user, /*$spellcheck,*/ $outputlanguage, $doc);
