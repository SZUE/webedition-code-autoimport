<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
if(defined('CUSTOMER_TABLE')){
	$name = new weTagData_sqlColAttribute('name', CUSTOMER_TABLE, true, [], '');
	$size = new weTagData_textAttribute('size', false, '');
	$maxlength = new weTagData_textAttribute('maxlength', false, '');
	$rows = new weTagData_textAttribute('rows', false, '');
	$cols = new weTagData_textAttribute('cols', false, '');
	$onchange = new weTagData_textAttribute('onchange', false, '');
	$choice = new weTagData_choiceAttribute('choice', weTagData_selectAttribute::getTrueFalse(), false, false, '');
	$checked = new weTagData_choiceAttribute('checked', weTagData_selectAttribute::getTrueFalse(), false, false, '');
	$value = new weTagData_textAttribute('value', false, '');
	$values = new weTagData_textAttribute('values', false, '');
	$dateformat = new weTagData_textAttribute('dateformat', false, '');
	$xml = new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, '');
	$id = new weTagData_textAttribute('id', false, '');
	$removefirstparagraph = new weTagData_selectAttribute('removefirstparagraph', weTagData_selectAttribute::getTrueFalse(), false, '');
	$autofill = new weTagData_selectAttribute('autofill', [new weTagDataOption('true'),
		], false, '');
	$parentid = new weTagData_selectorAttribute('parentid', FILE_TABLE, weTagData_selectorAttribute::FOLDER, false, 'customer');
	$width = new weTagData_textAttribute('width', false, 'customer');
	$height = new weTagData_textAttribute('height', false, 'customer');
	$quality = new weTagData_selectAttribute('quality', [new weTagDataOption('0'),
		new weTagDataOption('1'),
		new weTagDataOption('2'),
		new weTagDataOption('3'),
		new weTagDataOption('4'),
		new weTagDataOption('5'),
		new weTagDataOption('6'),
		new weTagDataOption('7'),
		new weTagDataOption('8'),
		new weTagDataOption('9'),
		new weTagDataOption('10'),
		], false, 'customer');
	$keepratio = new weTagData_selectAttribute('keepratio', weTagData_selectAttribute::getTrueFalse(), false, 'customer');
	$maximize = new weTagData_selectAttribute('maximize', weTagData_selectAttribute::getTrueFalse(), false, 'customer');
	$bordercolor = new weTagData_textAttribute('bordercolor', false, 'customer');
	$checkboxstyle = new weTagData_textAttribute('checkboxstyle', false, 'customer');
	$inputstyle = new weTagData_textAttribute('inputstyle', false, 'customer');
	$checkboxclass = new weTagData_textAttribute('checkboxclass', false, 'customer');
	$inputclass = new weTagData_textAttribute('inputclass', false, 'customer');
	$checkboxtext = new weTagData_textAttribute('checkboxtext', false, 'customer');
	$showcontrol = new weTagData_selectAttribute('showcontrol', weTagData_selectAttribute::getTrueFalse(), false, 'customer');
	$thumbnail = new weTagData_sqlRowAttribute('thumbnail', THUMBNAILS_TABLE, false, 'Name', '', '', '');
	$ascountry = new weTagData_selectAttribute('ascountry', weTagData_selectAttribute::getTrueFalse(), false, '');
	$aslanguage = new weTagData_selectAttribute('aslanguage', weTagData_selectAttribute::getTrueFalse(), false, '');
	$outputlanguage = new weTagData_textAttribute('outputlanguage', false, '');
	$languageautofill = new weTagData_selectAttribute('languageautofill', weTagData_selectAttribute::getTrueFalse(), false, '');
	$doc = new weTagData_selectAttribute('doc', [new weTagDataOption('self'),
		new weTagDataOption('top'),
		], false, '');

	$usevalue = new weTagData_selectAttribute('usevalue', weTagData_selectAttribute::getTrueFalse(), false, '');
	$minyear = new weTagData_textAttribute('minyear', false, '');
	$maxyear = new weTagData_textAttribute('maxyear', false, '');
	$pure = new weTagData_selectAttribute('pure', weTagData_selectAttribute::getTrueFalse(), false, '');
	$wysiwyg = new weTagData_selectAttribute('wysiwyg', weTagData_selectAttribute::getTrueFalse(), true, '');
	$autobr = new weTagData_selectAttribute('autobr', weTagData_selectAttribute::getTrueFalse(), false, '');
	$html = new weTagData_selectAttribute('html', weTagData_selectAttribute::getTrueFalse(), false, '');
	$htmlspecialchars = new weTagData_selectAttribute('htmlspecialchars', weTagData_selectAttribute::getTrueFalse(), false, '');
	$php = new weTagData_selectAttribute('php', weTagData_selectAttribute::getTrueFalse(), false, '');
	$abbr = new weTagData_selectAttribute('abbr', weTagData_selectAttribute::getTrueFalse(), false, '');
	$spellcheck = new weTagData_selectAttribute('spellcheck', weTagData_selectAttribute::getTrueFalse(), false, 'spellchecker');
	$commands = new weTagData_choiceAttribute('commands', we_wysiwyg_editor::getEditorCommands(true), false, true, '');


	$this->TypeAttribute = new weTagData_typeAttribute('type', [new weTagDataOption('textinput', false, '', [$name, $size, $maxlength, $value], [$name]),
		new weTagDataOption('textarea', false, '', [$name, $rows, $cols, $value, $pure, $wysiwyg, $autobr, $html, $htmlspecialchars, $php, $abbr, $spellcheck, $commands], [$name]),
		new weTagDataOption('checkbox', false, '', [$name, $checked], [$name]),
		new weTagDataOption('radio', false, '', [$name, $checked, $value], [$name]),
		new weTagDataOption('password', false, '', [$name, $size, $maxlength, $value], [$name]),
		new weTagDataOption('hidden', false, 'customer', [$name, $value, $autofill, $languageautofill, $doc, $usevalue, $htmlspecialchars], [$name]),
		new weTagDataOption('print', false, '', [$name, $dateformat, $ascountry, $aslanguage, $outputlanguage, $doc, $htmlspecialchars], [$name]),
		new weTagDataOption('select', false, '', [$name, $size, $value, $values], [$name]),
		new weTagDataOption('choice', false, '', [$name, $size, $maxlength, $value, $values], [$name]),
		new weTagDataOption('img', false, 'customer', [$name, $value, $id, $xml, $parentid, $width, $height, $quality, $keepratio, $maximize, $bordercolor, $checkboxstyle, $inputstyle, $checkboxclass, $inputclass, $checkboxtext, $showcontrol, $thumbnail], [$name, $parentid]),
		new weTagDataOption('date', false, '', [$name, $dateformat, $minyear, $maxyear, $value], [$name]),
		new weTagDataOption('country', false, '', [$name, $size, $doc, $value], [$name]),
		new weTagDataOption('language', false, '', [$name, $size, $doc, $value], [$name])], true, '');

	$this->Attributes = [$name, $size, $maxlength, $rows, $cols, $onchange, $choice, $checked, $value, $values, $dateformat, $xml, $id, $removefirstparagraph, $autofill,
		$parentid, $width, $height, $quality, $keepratio, $maximize, $bordercolor, $checkboxstyle, $inputstyle, $checkboxclass, $inputclass, $checkboxtext, $showcontrol,
		$thumbnail, $ascountry, $aslanguage, $outputlanguage, $languageautofill, $doc, $usevalue, $minyear, $maxyear, $pure, $wysiwyg, $autobr, $html, $htmlspecialchars, $php, $abbr, $spellcheck, $commands];
}
