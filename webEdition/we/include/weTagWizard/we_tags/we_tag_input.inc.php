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
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$name = new we_tagData_textAttribute('name', true, '');
$size = new we_tagData_textAttribute('size', false, '');
$maxlength = new we_tagData_textAttribute('maxlength', false, '');
$format = new we_tagData_textAttribute('format', false, '');
$mode = new we_tagData_selectAttribute('mode', [new we_tagData_option('add', false, ''), new we_tagData_option('replace', false, '')], false, '');
$value = new we_tagData_textAttribute('value', false, '');
$values = new we_tagData_textAttribute('values', false, '');
$html = new we_tagData_selectAttribute('html', we_tagData_selectAttribute::getTrueFalse(), false, '');
$htmlspecialchars = new we_tagData_selectAttribute('htmlspecialchars', we_tagData_selectAttribute::getTrueFalse(), false, '');
$php = new we_tagData_selectAttribute('php', we_tagData_selectAttribute::getTrueFalse(), false, '');
$num_format = new we_tagData_selectAttribute('num_format', [new we_tagData_option('german', false, ''), new we_tagData_option('english', false, ''), new we_tagData_option('french', false, ''), new we_tagData_option('swiss', false, '')], false, '');
$precision = new we_tagData_textAttribute('precision', false, '');
$win2iso = new we_tagData_selectAttribute('win2iso', we_tagData_selectAttribute::getTrueFalse(), false, '');
$reload = new we_tagData_selectAttribute('reload', we_tagData_selectAttribute::getTrueFalse(), false, '');
$seperator = new we_tagData_textAttribute('seperator', false, '');
$user = new we_tagData_multiSelectorAttribute('user', USER_TABLE, 'user,folder', 'username', false, 'users');
//$spellcheck = new weTagData_selectAttribute('spellcheck', weTagData_selectAttribute::getTrueFalse(), false, 'spellchecker');
$outputlanguage = new we_tagData_textAttribute('outputlanguage', false, '');
$doc = new we_tagData_selectAttribute('doc', [new we_tagData_option('self', false, ''), new we_tagData_option('top', false, '')], false, '');
$currentdate = new we_tagData_selectAttribute('currentdate', we_tagData_selectAttribute::getTrueFalse(), false, '');

$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('text', false, '', [$name, $size, $maxlength, $value, $html, $php, $num_format, $precision, $user, $htmlspecialchars, /*$spellcheck,*/], [$name]),
	new we_tagData_option('checkbox', false, '', [$name, $value, $reload, $user, $htmlspecialchars,], [$name]),
	new we_tagData_option('date', false, '', [$name, $format, $currentdate, $user, $htmlspecialchars,], [$name]),
	new we_tagData_option('choice', false, '', [$name, $size, $maxlength, $mode, $values, $reload, $seperator, $user, $htmlspecialchars,], [$name]),
	new we_tagData_option('select', false, '', [$name, $values, $htmlspecialchars,], [$name]),
	new we_tagData_option('country', false, '', [$name, $outputlanguage, $doc,], [$name]),
	new we_tagData_option('language', false, '', [$name, $outputlanguage, $doc], [$name])], true, '');

$this->Attributes = [$name, $size, $maxlength, $format, $mode, $value, $values, $html, $htmlspecialchars, $php, $num_format, $precision, $win2iso, $reload,
	$seperator, $user, /*$spellcheck,*/ $outputlanguage, $doc];
