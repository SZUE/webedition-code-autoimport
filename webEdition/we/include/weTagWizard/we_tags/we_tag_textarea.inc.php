<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$name = new weTagData_textAttribute('name', true, '');
$cols = new weTagData_textAttribute('cols', false, '');
$rows = new weTagData_textAttribute('rows', false, '');
$autobr = new weTagData_selectAttribute('autobr', weTagData_selectAttribute::getTrueFalse(), false, '');
$importrtf = new weTagData_selectAttribute('importrtf', weTagData_selectAttribute::getTrueFalse(), false, '');
$width = new weTagData_textAttribute('width', false, '');
$height = new weTagData_textAttribute('height', false, '');
$bgcolor = new weTagData_textAttribute('bgcolor', false, '');
$class = new weTagData_textAttribute('class', false, '');
if(defined("FILE_TABLE")){
	$editorcss = new weTagData_selectorAttribute('editorcss', FILE_TABLE, 'text/css', false, '');
}
$ignoredocumentcss = new weTagData_selectAttribute('ignoredocumentcss', weTagData_selectAttribute::getTrueFalse(), false, '');
$html = new weTagData_selectAttribute('html', weTagData_selectAttribute::getTrueFalse(), false, '');
$htmlspecialchars = new weTagData_selectAttribute('htmlspecialchars', weTagData_selectAttribute::getTrueFalse(), false, '');
$php = new weTagData_selectAttribute('php', weTagData_selectAttribute::getTrueFalse(), false, '');
$commands = new weTagData_choiceAttribute('commands', we_wysiwyg::getEditorCommands(true), false, true, '');
$contextmenu = new weTagData_choiceAttribute('contextmenu', we_wysiwyg::getEditorCommands(true), false, true, '');
$fontnames = new weTagData_choiceAttribute('fontnames', array(new weTagDataOption('arial'),
	new weTagDataOption('courier'),
	new weTagDataOption('tahoma'),
	new weTagDataOption('times'),
	new weTagDataOption('verdana'),
	new weTagDataOption('wingdings'),
	), false, true, '');
$xml = new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, '');
$abbr = new weTagData_selectAttribute('abbr', weTagData_selectAttribute::getTrueFalse(), false, '');
$removefirstparagraph = new weTagData_selectAttribute('removefirstparagraph', weTagData_selectAttribute::getTrueFalse(), false, '');
$inlineedit = new weTagData_selectAttribute('inlineedit', weTagData_selectAttribute::getTrueFalse(), false, '');
$buttonpos = new weTagData_choiceAttribute('buttonpos', array(new weTagDataOption('top'),
	new weTagDataOption('bottom'),
	), false, false, '');
$win2iso = new weTagData_selectAttribute('win2iso', weTagData_selectAttribute::getTrueFalse(), false, '');
$classes = new weTagData_textAttribute('classes', false, '');
$spellcheck = new weTagData_selectAttribute('spellcheck', weTagData_selectAttribute::getTrueFalse(), false, 'spellchecker');
$templates = new weTagData_textAttribute('tinyparams', false, '');
$tinyparams = new weTagData_textAttribute('templates', false, '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');

$this->TypeAttribute = new weTagData_typeAttribute('wysiwyg', array(
	new weTagDataOption('true', false, '', array($name, $cols, $rows, $autobr, $width, $height, $class, $bgcolor, $editorcss, $ignoredocumentcss, $htmlspecialchars, $commands, $contextmenu, $fontnames, $abbr, $removefirstparagraph, $inlineedit, $buttonpos, $win2iso, $classes, $spellcheck, $templates, $tinyparams), array($name)),
	new weTagDataOption('false', false, '', array($name, $cols, $rows, $class, $autobr, $html, $htmlspecialchars, $php, $abbr, $spellcheck), array($name))), false, '');

$this->Attributes = array($name, $cols, $rows, $class, $autobr, $importrtf, $width, $height, $bgcolor, $editorcss, $ignoredocumentcss, $html, $htmlspecialchars, $php, $commands, $contextmenu, $fontnames, $xml, $abbr,
	$removefirstparagraph, $inlineedit, $buttonpos, $win2iso, $classes, $spellcheck, $templates, $tinyparams);
