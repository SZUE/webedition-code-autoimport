<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$name = new weTagData_textAttribute('name', true, '');
$property = new weTagData_selectAttribute('556', 'property', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$checked = new weTagData_selectAttribute('557', 'checked', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$editable = new weTagData_selectAttribute('558', 'editable', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$xml = new weTagData_selectAttribute('628', 'xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$removefirstparagraph = new weTagData_selectAttribute('560', 'removefirstparagraph', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$size = new weTagData_textAttribute('561', 'size', false, '');
$maxlength = new weTagData_textAttribute('562', 'maxlength', false, '');
$format = new weTagData_textAttribute('563', 'format', false, '');
$value = new weTagData_textAttribute('564', 'value', false, '');
$values = new weTagData_textAttribute('565', 'values', false, '');
$hidden = new weTagData_selectAttribute('566', 'hidden', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$currentdate = new weTagData_selectAttribute('567', 'currentdate', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$cols = new weTagData_textAttribute('568', 'cols', false, '');
$rows = new weTagData_textAttribute('569', 'rows', false, '');
$pure = new weTagData_selectAttribute('570', 'pure', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$autobr = new weTagData_selectAttribute('571', 'autobr', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$width = new weTagData_textAttribute('572', 'width', false, '');
$height = new weTagData_textAttribute('573', 'height', false, '');
$bgcolor = new weTagData_textAttribute('574', 'bgcolor', false, '');
$class = new weTagData_textAttribute('575', 'class', false, '');
$style = new weTagData_textAttribute('576', 'style', false, '');
$classes = new weTagData_textAttribute('735', 'classes', false, '');
$hideautobr = new weTagData_selectAttribute('577', 'hideautobr', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$wysiwyg = new weTagData_selectAttribute('578', 'wysiwyg', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$commands = new weTagData_textAttribute('579', 'commands', false, '');
$fontnames = new weTagData_textAttribute('580', 'fontnames', false, '');
$parentid = new weTagData_selectorAttribute('768', 'parentid', FILE_TABLE, 'folder', true, 'customer');
$quality = new weTagData_selectAttribute('769', 'quality', array(new weTagDataOption('0', false, ''), new weTagDataOption('1', false, ''), new weTagDataOption('2', false, ''), new weTagDataOption('3', false, ''), new weTagDataOption('4', false, ''), new weTagDataOption('5', false, ''), new weTagDataOption('6', false, ''), new weTagDataOption('7', false, ''), new weTagDataOption('8', false, ''), new weTagDataOption('9', false, ''), new weTagDataOption('10', false, '')), false, 'customer');
$keepratio = new weTagData_selectAttribute('770', 'keepratio', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'customer');
$maximize = new weTagData_selectAttribute('771', 'maximize', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'customer');
$bordercolor = new weTagData_textAttribute('773', 'bordercolor', false, 'customer');
$checkboxstyle = new weTagData_textAttribute('774', 'checkboxstyle', false, 'customer');
$checkboxclass = new weTagData_textAttribute('775', 'checkboxclass', false, 'customer');
$inputstyle = new weTagData_textAttribute('776', 'inputstyle', false, 'customer');
$inputclass = new weTagData_textAttribute('777', 'inputclass', false, 'customer');
$checkboxtext = new weTagData_textAttribute('778', 'checkboxtext', false, 'customer');
$doc = new weTagData_selectAttribute('855', 'doc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')), false, '');
$minyear = new weTagData_textAttribute('876', 'minyear', false, '');
$maxyear = new weTagData_textAttribute('877', 'maxyear', false, '');

$this->TypeAttribute = new weTagData_typeAttribute('555', 'type', array(
new weTagDataOption('textinput', false, '', array($name, $property, $editable, $size, $maxlength, $value, $class, $style), array($name)),
 new weTagDataOption('textarea', false, '', array($name, $property, $editable, $value, $cols, $rows, $autobr, $width, $height, $bgcolor, $class, $style, $hideautobr, $wysiwyg, $commands, $fontnames, $classes), array($name)),
 new weTagDataOption('checkbox', false, '', array($name, $property, $checked, $editable), array($name)),
	new weTagDataOption('radio', false, '', array($name, $property, $checked, $editable, $value), array($name)),
 new weTagDataOption('choice', false, '', array($name, $property, $editable, $size, $maxlength, $value, $values, $class, $style), array($name)),
 new weTagDataOption('select', false, '', array($name, $property, $editable, $size, $value, $values, $class, $style), array($name)),
 new weTagDataOption('hidden', false, '', array($name, $property), array($name)),
	new weTagDataOption('date', false, '', array($name, $property, $editable, $format, $value, $minyear, $maxyear, $hidden), array($name)),
 new weTagDataOption('password', false, '', array(array())),
 new weTagDataOption('img', false, 'customer', array($name, $editable, $size, $value, $width, $height, $parentid, $quality, $keepratio, $maximize, $bordercolor, $checkboxstyle, $checkboxclass, $inputstyle, $inputclass, $checkboxtext), array($name, $parentid)),
 new weTagDataOption('flashmovie', false, 'customer', array($name, $editable, $size, $value, $width, $height, $parentid, $quality, $keepratio, $maximize, $bordercolor, $checkboxstyle, $checkboxclass, $inputstyle, $inputclass, $checkboxtext), array($name, $parentid)),
 new weTagDataOption('quicktime', false, 'customer', array($name, $editable, $size, $value, $width, $height, $parentid, $quality, $keepratio, $maximize, $bordercolor, $checkboxstyle, $checkboxclass, $inputstyle, $inputclass, $checkboxtext), array($name, $parentid)),
 new weTagDataOption('binary', false, 'customer', array($name, $editable, $size, $value, $parentid, $bordercolor, $checkboxstyle, $checkboxclass, $inputstyle, $inputclass, $checkboxtext), array($name, $parentid)),
 new weTagDataOption('country', false, '', array($name, $size, $class, $style, $doc, $value), array($name)),
 new weTagDataOption('language', false, '', array($name, $size, $class, $style, $doc, $value), array($name))),
 true, '');

$this->Attributes = array($name, $property, $checked, $editable, $xml, $removefirstparagraph, $size, $maxlength, $format, $value, $values, $hidden, $currentdate, $cols,
	$rows, $pure, $autobr, $width, $bgcolor, $class, $style, $classes, $wysiwyg, $commands, $fontnames, $parentid, $quality, $keepratio, $maximize, $bordercolor, $checkboxstyle,
	$checkboxclass, $inputstyle, $inputclass, $checkboxtext, $doc, $minyear, $maxyear);
