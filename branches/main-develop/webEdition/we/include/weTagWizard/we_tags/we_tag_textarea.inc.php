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
/*$cols = new weTagData_textAttribute('cols', false, '');
$rows = new weTagData_textAttribute('rows', false, '');*/
$autobr = new we_tagData_selectAttribute('autobr', we_tagData_selectAttribute::getTrueFalse(), false, '');
$importrtf = new we_tagData_selectAttribute('importrtf', we_tagData_selectAttribute::getTrueFalse(), false, '');
$width = new we_tagData_textAttribute('width', false, '');
$height = new we_tagData_textAttribute('height', false, '');
$bgcolor = new we_tagData_textAttribute('bgcolor', false, '');
$class = new we_tagData_textAttribute('class', false, '');
if(defined('FILE_TABLE')){
	$editorcss = new we_tagData_selectorAttribute('editorcss', FILE_TABLE, we_base_ContentTypes::CSS, false, '');
	$imagestartid = new we_tagData_selectorAttribute('imagestartid', FILE_TABLE, we_tagData_selectorAttribute::FOLDER, false, '');
}
$ignoredocumentcss = new we_tagData_selectAttribute('ignoredocumentcss', we_tagData_selectAttribute::getTrueFalse(), false, '');
$html = new we_tagData_selectAttribute('html', we_tagData_selectAttribute::getTrueFalse(), false, '');
$htmlspecialchars = new we_tagData_selectAttribute('htmlspecialchars', we_tagData_selectAttribute::getTrueFalse(), false, '');
$php = new we_tagData_selectAttribute('php', we_tagData_selectAttribute::getTrueFalse(), false, '');
$commands = new we_tagData_choiceAttribute('commands', we_wysiwyg_editor::getEditorCommands(true), false, true, '');
$contextmenu = new we_tagData_choiceAttribute('contextmenu', we_wysiwyg_editor::getEditorCommands(true), false, true, '');
$menu = new we_tagData_choiceAttribute('menu', we_wysiwyg_editor::getEditorCommands(true), false, true, '');
$fontnames = new we_tagData_choiceAttribute('fontnames', we_wysiwyg_editor::getAttributeOptions('fontnames', true), false, true, '');
$fontsizes = new we_tagData_choiceAttribute('fontsizes', we_wysiwyg_editor::getAttributeOptions('fontsizes', true), false, true, '');
$formats = new we_tagData_choiceAttribute('formats', we_wysiwyg_editor::getAttributeOptions('formats', true), false, true, '');
$xml = new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, '');
$abbr = new we_tagData_selectAttribute('abbr', we_tagData_selectAttribute::getTrueFalse(), false, '');
$removefirstparagraph = new we_tagData_selectAttribute('removefirstparagraph', we_tagData_selectAttribute::getTrueFalse(), false, '');
$inlineedit = new we_tagData_selectAttribute('inlineedit', we_tagData_selectAttribute::getTrueFalse(), false, '');
$buttonpos = new we_tagData_choiceAttribute('buttonpos', [new we_tagData_option('top'),
	new we_tagData_option('bottom'),
 ], false, false, '');
$win2iso = new we_tagData_selectAttribute('win2iso', we_tagData_selectAttribute::getTrueFalse(), false, '');
$classes = new we_tagData_textAttribute('classes', false, '');
//$spellcheck = new weTagData_selectAttribute('spellcheck', weTagData_selectAttribute::getTrueFalse(), false, 'spellchecker');
$tinyparams = new we_tagData_textAttribute('tinyparams', false, '');
$templates = new we_tagData_textAttribute('templates', false, '');
$gallerytemplates = new we_tagData_textAttribute('gallerytemplates', false, '');

$this->TypeAttribute = new we_tagData_typeAttribute('wysiwyg', [new we_tagData_option('true', false, '', array_filter([$name, $cols, $rows, $autobr, $width, $height, $class, $bgcolor, $editorcss, $ignoredocumentcss, $htmlspecialchars, $commands, (IS_TINYMCE_4 ? $menu : false), $contextmenu, $fontnames, $fontsizes, $formats, $abbr, $removefirstparagraph, $inlineedit, $buttonpos, $win2iso, $classes, /*$spellcheck,*/ $templates, $gallerytemplates, $tinyparams, $imagestartid]), [$name]),
	new we_tagData_option('false', false, '', [$name, $cols, $rows, $class, $autobr, $html, $htmlspecialchars, $php, $abbr/*, $spellcheck*/], [$name])], false, '');

$this->Attributes = array_filter([$name, $cols, $rows, $class, $autobr, $importrtf, $width, $height, $bgcolor, $editorcss, $ignoredocumentcss, $html, $htmlspecialchars, $php, $commands, (IS_TINYMCE_4 ? $menu : false), $contextmenu, $fontnames, $fontsizes, $formats, $xml, $abbr,
	$removefirstparagraph, $inlineedit, $buttonpos, $win2iso, $classes, /*$spellcheck,*/ $templates, $gallerytemplates, $tinyparams, $imagestartid]);
