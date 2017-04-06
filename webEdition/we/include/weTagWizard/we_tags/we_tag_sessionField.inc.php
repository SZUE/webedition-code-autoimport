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
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
if(defined('CUSTOMER_TABLE')){
	$name = new we_tagData_sqlColAttribute('name', CUSTOMER_TABLE, true, [], '');
	$size = new we_tagData_textAttribute('size', false, '');
	$maxlength = new we_tagData_textAttribute('maxlength', false, '');
	$rows = new we_tagData_textAttribute('rows', false, '');
	$cols = new we_tagData_textAttribute('cols', false, '');
	$onchange = new we_tagData_textAttribute('onchange', false, '');
	$choice = new we_tagData_choiceAttribute('choice', we_tagData_selectAttribute::getTrueFalse(), false, false, '');
	$checked = new we_tagData_choiceAttribute('checked', we_tagData_selectAttribute::getTrueFalse(), false, false, '');
	$value = new we_tagData_textAttribute('value', false, '');
	$values = new we_tagData_textAttribute('values', false, '');
	$dateformat = new we_tagData_textAttribute('dateformat', false, '');
	$xml = new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, '');
	$id = new we_tagData_textAttribute('id', false, '');
	$removefirstparagraph = new we_tagData_selectAttribute('removefirstparagraph', we_tagData_selectAttribute::getTrueFalse(), false, '');
	$autofill = new we_tagData_selectAttribute('autofill', [new we_tagData_option('true'),
		], false, '');
	$parentid = new we_tagData_selectorAttribute('parentid', FILE_TABLE, we_tagData_selectorAttribute::FOLDER, false, 'customer');
	$width = new we_tagData_textAttribute('width', false, 'customer');
	$height = new we_tagData_textAttribute('height', false, 'customer');
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
	$inputstyle = new we_tagData_textAttribute('inputstyle', false, 'customer');
	$checkboxclass = new we_tagData_textAttribute('checkboxclass', false, 'customer');
	$inputclass = new we_tagData_textAttribute('inputclass', false, 'customer');
	$checkboxtext = new we_tagData_textAttribute('checkboxtext', false, 'customer');
	$showcontrol = new we_tagData_selectAttribute('showcontrol', we_tagData_selectAttribute::getTrueFalse(), false, 'customer');
	$thumbnail = new we_tagData_sqlRowAttribute('thumbnail', THUMBNAILS_TABLE, false, 'Name', '', '', '');
	$ascountry = new we_tagData_selectAttribute('ascountry', we_tagData_selectAttribute::getTrueFalse(), false, '');
	$aslanguage = new we_tagData_selectAttribute('aslanguage', we_tagData_selectAttribute::getTrueFalse(), false, '');
	$outputlanguage = new we_tagData_textAttribute('outputlanguage', false, '');
	$languageautofill = new we_tagData_selectAttribute('languageautofill', we_tagData_selectAttribute::getTrueFalse(), false, '');
	$doc = new we_tagData_selectAttribute('doc', [new we_tagData_option('self'),
		new we_tagData_option('top'),
		], false, '');

	$usevalue = new we_tagData_selectAttribute('usevalue', we_tagData_selectAttribute::getTrueFalse(), false, '');
	$minyear = new we_tagData_textAttribute('minyear', false, '');
	$maxyear = new we_tagData_textAttribute('maxyear', false, '');
	$pure = new we_tagData_selectAttribute('pure', we_tagData_selectAttribute::getTrueFalse(), false, '');
	$wysiwyg = new we_tagData_selectAttribute('wysiwyg', we_tagData_selectAttribute::getTrueFalse(), true, '');
	$autobr = new we_tagData_selectAttribute('autobr', we_tagData_selectAttribute::getTrueFalse(), false, '');
	$html = new we_tagData_selectAttribute('html', we_tagData_selectAttribute::getTrueFalse(), false, '');
	$htmlspecialchars = new we_tagData_selectAttribute('htmlspecialchars', we_tagData_selectAttribute::getTrueFalse(), false, '');
	$php = new we_tagData_selectAttribute('php', we_tagData_selectAttribute::getTrueFalse(), false, '');
	$abbr = new we_tagData_selectAttribute('abbr', we_tagData_selectAttribute::getTrueFalse(), false, '');
	//$spellcheck = new weTagData_selectAttribute('spellcheck', weTagData_selectAttribute::getTrueFalse(), false, 'spellchecker');
	$commands = new we_tagData_choiceAttribute('commands', we_wysiwyg_editor::getEditorCommands(true), false, true, '');

	$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('textinput', false, '', [$name, $size, $maxlength, $value], [$name]),
		new we_tagData_option('textarea', false, '', [$name, $rows, $cols, $value, $pure, $wysiwyg, $autobr, $html, $htmlspecialchars, $php, $abbr, /*$spellcheck,*/ $commands], [$name]),
		new we_tagData_option('checkbox', false, '', [$name, $checked], [$name]),
		new we_tagData_option('radio', false, '', [$name, $checked, $value], [$name]),
		new we_tagData_option('password', false, '', [$name, $size, $maxlength, $value], [$name]),
		new we_tagData_option('hidden', false, 'customer', [$name, $value, $autofill, $languageautofill, $doc, $usevalue, $htmlspecialchars], [$name]),
		new we_tagData_option('print', false, '', [$name, $dateformat, $ascountry, $aslanguage, $outputlanguage, $doc, $htmlspecialchars], [$name]),
		new we_tagData_option('select', false, '', [$name, $size, $value, $values], [$name]),
		new we_tagData_option('choice', false, '', [$name, $size, $maxlength, $value, $values], [$name]),
		new we_tagData_option('img', false, 'customer', [$name, $value, $id, $xml, $parentid, $width, $height, $quality, $keepratio, $maximize, $bordercolor, $checkboxstyle, $inputstyle, $checkboxclass, $inputclass, $checkboxtext, $showcontrol, $thumbnail], [$name, $parentid]),
		new we_tagData_option('date', false, '', [$name, $dateformat, $minyear, $maxyear, $value], [$name]),
		new we_tagData_option('country', false, '', [$name, $size, $doc, $value], [$name]),
		new we_tagData_option('language', false, '', [$name, $size, $doc, $value], [$name])], true, '');

	$this->Attributes = [$name, $size, $maxlength, $rows, $cols, $onchange, $choice, $checked, $value, $values, $dateformat, $xml, $id, $removefirstparagraph, $autofill,
		$parentid, $width, $height, $quality, $keepratio, $maximize, $bordercolor, $checkboxstyle, $inputstyle, $checkboxclass, $inputclass, $checkboxtext, $showcontrol,
		$thumbnail, $ascountry, $aslanguage, $outputlanguage, $languageautofill, $doc, $usevalue, $minyear, $maxyear, $pure, $wysiwyg, $autobr, $html, $htmlspecialchars, $php, $abbr, /*$spellcheck,*/ $commands];
}
