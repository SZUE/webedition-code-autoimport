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
$property = new we_tagData_selectAttribute('property', we_tagData_selectAttribute::getTrueFalse(), false, '');
$checked = new we_tagData_selectAttribute('checked', we_tagData_selectAttribute::getTrueFalse(), false, '');
$editable = new we_tagData_selectAttribute('editable', we_tagData_selectAttribute::getTrueFalse(), false, '');
$xml = new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, '');
$removefirstparagraph = new we_tagData_selectAttribute('removefirstparagraph', we_tagData_selectAttribute::getTrueFalse(), false, '');
$size = new we_tagData_textAttribute('size');
$maxlength = new we_tagData_textAttribute('maxlength');
$format = new we_tagData_textAttribute('format');
$value = new we_tagData_textAttribute('value');
$values = new we_tagData_textAttribute('values');
$hidden = new we_tagData_selectAttribute('hidden', we_tagData_selectAttribute::getTrueFalse(), false, '');
$currentdate = new we_tagData_selectAttribute('currentdate', we_tagData_selectAttribute::getTrueFalse(), false, '');
$cols = new we_tagData_textAttribute('cols');
$rows = new we_tagData_textAttribute('rows');
$pure = new we_tagData_selectAttribute('pure', we_tagData_selectAttribute::getTrueFalse(), false, '');
$autobr = new we_tagData_selectAttribute('autobr', we_tagData_selectAttribute::getTrueFalse(), false, '');
$width = new we_tagData_textAttribute('width');
$height = new we_tagData_textAttribute('height');
$bgcolor = new we_tagData_textAttribute('bgcolor');
$class = new we_tagData_textAttribute('class');
$style = new we_tagData_textAttribute('style');
$classes = new we_tagData_textAttribute('classes');
$hideautobr = new we_tagData_selectAttribute('hideautobr', we_tagData_selectAttribute::getTrueFalse(), false, '');
$wysiwyg = new we_tagData_selectAttribute('wysiwyg', we_tagData_selectAttribute::getTrueFalse(), false, '');
$buttonpos = new we_tagData_choiceAttribute('buttonpos', [new we_tagData_option('top'), new we_tagData_option('bottom')], false, false, '');
$commands = new we_tagData_choiceAttribute('commands', we_wysiwyg_editor::getEditorCommands(true), false, true, '');
$contextmenu = new we_tagData_choiceAttribute('contextmenu', we_wysiwyg_editor::getEditorCommands(true), false, true, '');
$editorcss = (defined('FILE_TABLE') ? new we_tagData_selectorAttribute('editorcss', FILE_TABLE, we_base_ContentTypes::CSS, false, '') : null);
$ignoredocumentcss = new we_tagData_selectAttribute('ignoredocumentcss', we_tagData_selectAttribute::getTrueFalse(), false, '');
$fontnames = new we_tagData_choiceAttribute('fontnames', [new we_tagData_option('arial'),
	new we_tagData_option('courier'),
	new we_tagData_option('tahoma'),
	new we_tagData_option('times'),
	new we_tagData_option('verdana'),
	new we_tagData_option('wingdings'),
	], false, true, '');
$parentid = new we_tagData_selectorAttribute('parentid', FILE_TABLE, we_tagData_selectorAttribute::FOLDER, true, 'customer');
$quality = new we_tagData_selectAttribute('quality', [new we_tagData_option('0'),
	new we_tagData_option('1'),
	new we_tagData_option('2'),
	new we_tagData_option('3'),
	new we_tagData_option('4'),
	new we_tagData_option('5'),
	new we_tagData_option('6'),
	new we_tagData_option('7'),
	new we_tagData_option('8'),
	new we_tagData_option('9'),
	new we_tagData_option('10'),
	], false, 'customer');
$keepratio = new we_tagData_selectAttribute('keepratio', we_tagData_selectAttribute::getTrueFalse(), false, 'customer');
$maximize = new we_tagData_selectAttribute('maximize', we_tagData_selectAttribute::getTrueFalse(), false, 'customer');
$bordercolor = new we_tagData_textAttribute('bordercolor', false, 'customer');
$checkboxstyle = new we_tagData_textAttribute('checkboxstyle', false, 'customer');
$checkboxclass = new we_tagData_textAttribute('checkboxclass', false, 'customer');
$inputstyle = new we_tagData_textAttribute('inputstyle', false, 'customer');
$inputclass = new we_tagData_textAttribute('inputclass', false, 'customer');
$checkboxtext = new we_tagData_textAttribute('checkboxtext', false, 'customer');
$doc = new we_tagData_selectAttribute('doc', [new we_tagData_option('self'),
	new we_tagData_option('top'),
	], false, '');
$minyear = new we_tagData_textAttribute('minyear');
$maxyear = new we_tagData_textAttribute('maxyear');
$thumbnail = new we_tagData_sqlRowAttribute('thumbnail', THUMBNAILS_TABLE, false, 'Name', '', '', '');


$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('textinput', false, '', [$name, $property, $editable, $size, $maxlength, $value,
		$class, $style], [$name]),
	new we_tagData_option('textarea', false, '', [$name, $property, $editable, $value, $cols, $rows, $autobr, $width, $height, $bgcolor, $class, $style, $hideautobr,
		$wysiwyg, $wysiwyg, $buttonpos, $ignoredocumentcss, $editorcss, $commands, $contextmenu, $fontnames, $classes], [$name]),
	new we_tagData_option('checkbox', false, '', [$name, $property, $checked, $editable], [$name]),
	new we_tagData_option('radio', false, '', [$name, $property, $checked, $editable, $value], [$name]),
	new we_tagData_option('choice', false, '', [$name, $property, $editable, $size, $maxlength, $value, $values, $class, $style], [$name]),
	new we_tagData_option('select', false, '', [$name, $property, $editable, $size, $value, $values, $class, $style], [$name]),
	new we_tagData_option('hidden', false, '', [$name, $property, $value], [$name]),
	new we_tagData_option('print', false, '', [$name, $property], [$name]),
	new we_tagData_option('date', false, '', [$name, $property, $editable, $format, $value, $minyear, $maxyear, $hidden], [$name]),
	new we_tagData_option('password', false, '', [[]]),
	new we_tagData_option('img', false, 'customer', [$name, $editable, $size, $value, $width, $height, $thumbnail, $parentid, $quality, $keepratio, $maximize, $bordercolor,
		$checkboxstyle, $checkboxclass, $inputstyle, $inputclass, $checkboxtext], [$name, $parentid]),
	new we_tagData_option('flashmovie', false, 'customer', [$name, $editable, $size, $value, $width, $height, $parentid, $quality, $keepratio, $maximize, $bordercolor,
		$checkboxstyle, $checkboxclass, $inputstyle, $inputclass, $checkboxtext], [$name, $parentid]),
	new we_tagData_option('binary', false, 'customer', [$name, $editable, $size, $value, $parentid, $bordercolor, $checkboxstyle, $checkboxclass, $inputstyle, $inputclass,
		$checkboxtext], [$name, $parentid]),
	new we_tagData_option('country', false, '', [$name, $size, $class, $style, $doc, $value], [$name]),
	new we_tagData_option('language', false, '', [$name, $size, $class, $style, $doc, $value], [$name])], true, '');

$this->Attributes = [$name, $property, $checked, $editable, $xml, $removefirstparagraph, $size, $maxlength, $format, $value, $values, $hidden, $currentdate, $cols,
	$rows, $pure, $autobr, $width, $height, $bgcolor, $class, $style, $wysiwyg, $buttonpos, $ignoredocumentcss, $editorcss, $commands, $contextmenu, $classes, $fontnames,
	$parentid, $quality, $keepratio, $maximize, $thumbnail, $bordercolor,
	$checkboxstyle, $checkboxclass, $inputstyle, $inputclass, $checkboxtext, $doc, $minyear, $maxyear];
