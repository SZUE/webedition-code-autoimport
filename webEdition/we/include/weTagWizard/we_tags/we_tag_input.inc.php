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
$mode = new weTagData_selectAttribute('mode', [new weTagDataOption('add', false, ''), new weTagDataOption('replace', false, '')], false, '');
$value = new weTagData_textAttribute('value', false, '');
$values = new weTagData_textAttribute('values', false, '');
$html = new weTagData_selectAttribute('html', weTagData_selectAttribute::getTrueFalse(), false, '');
$htmlspecialchars = new weTagData_selectAttribute('htmlspecialchars', weTagData_selectAttribute::getTrueFalse(), false, '');
$php = new weTagData_selectAttribute('php', weTagData_selectAttribute::getTrueFalse(), false, '');
$num_format = new weTagData_selectAttribute('num_format', [new weTagDataOption('german', false, ''), new weTagDataOption('english', false, ''), new weTagDataOption('french', false, ''), new weTagDataOption('swiss', false, '')], false, '');
$precision = new weTagData_textAttribute('precision', false, '');
$win2iso = new weTagData_selectAttribute('win2iso', weTagData_selectAttribute::getTrueFalse(), false, '');
$reload = new weTagData_selectAttribute('reload', weTagData_selectAttribute::getTrueFalse(), false, '');
$seperator = new weTagData_textAttribute('seperator', false, '');
$user = new weTagData_multiSelectorAttribute('user', USER_TABLE, 'user,folder', 'username', false, 'users');
//$spellcheck = new weTagData_selectAttribute('spellcheck', weTagData_selectAttribute::getTrueFalse(), false, 'spellchecker');
$outputlanguage = new weTagData_textAttribute('outputlanguage', false, '');
$doc = new weTagData_selectAttribute('doc', [new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')], false, '');
$currentdate = new weTagData_selectAttribute('currentdate', weTagData_selectAttribute::getTrueFalse(), false, '');

$this->TypeAttribute = new weTagData_typeAttribute('type', [new weTagDataOption('text', false, '', [$name, $size, $maxlength, $value, $html, $php, $num_format, $precision, $user, $htmlspecialchars, $spellcheck,], [$name]),
	new weTagDataOption('checkbox', false, '', [$name, $value, $reload, $user, $htmlspecialchars,], [$name]),
	new weTagDataOption('date', false, '', [$name, $format, $currentdate, $user, $htmlspecialchars,], [$name]),
	new weTagDataOption('choice', false, '', [$name, $size, $maxlength, $mode, $values, $reload, $seperator, $user, $htmlspecialchars,], [$name]),
	new weTagDataOption('select', false, '', [$name, $values, $htmlspecialchars,], [$name]),
	new weTagDataOption('country', false, '', [$name, $outputlanguage, $doc,], [$name]),
	new weTagDataOption('language', false, '', [$name, $outputlanguage, $doc], [$name])], true, '');

$this->Attributes = [$name, $size, $maxlength, $format, $mode, $value, $values, $html, $htmlspecialchars, $php, $num_format, $precision, $win2iso, $reload,
	$seperator, $user, $spellcheck, $outputlanguage, $doc];
