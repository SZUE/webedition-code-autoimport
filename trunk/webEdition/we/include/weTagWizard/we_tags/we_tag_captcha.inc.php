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

$this->Attributes[] = new weTagData_textAttribute('width', true, '');
$this->Attributes[] = new weTagData_textAttribute('height', true, '');
$this->Attributes[] = new weTagData_textAttribute('maxlength', false, '');

$this->Attributes[] = new weTagData_selectAttribute('subset', array(new weTagDataOption('alphanum'),
	new weTagDataOption('alpha'),
	new weTagDataOption('num'),
	), false, '');
$this->Attributes[] = new weTagData_textAttribute('skip', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('fontcolor', array(new weTagDataOption('#000000'),
	new weTagDataOption('#ffffff'),
	new weTagDataOption('#ff0000'),
	new weTagDataOption('#00ff00'),
	new weTagDataOption('#0000ff'),
	new weTagDataOption('#ffff00'),
	new weTagDataOption('#ff00ff'),
	new weTagDataOption('#00ffff'),
	), false, true, '');
$this->Attributes[] = new weTagData_textAttribute('fontsize', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('bgcolor', array(new weTagDataOption('#ffffff'),
	new weTagDataOption('#cccccc'),
	new weTagDataOption('#888888'),
	), false, false, '');
$this->Attributes[] = new weTagData_selectAttribute('transparent', array(new weTagDataOption('false'),
	new weTagDataOption('true'),
	), false, '');
$this->Attributes[] = new weTagData_choiceAttribute('style', array(new weTagDataOption('strikeout'),
	new weTagDataOption('fullcircle'),
	new weTagDataOption('fullrectangle'),
	new weTagDataOption('outlinecircle'),
	new weTagDataOption('outlinerectangle'),
	), false, true, '');
$this->Attributes[] = new weTagData_choiceAttribute('stylecolor', array(new weTagDataOption('#cccccc'),
	new weTagDataOption('#ff0000'),
	new weTagDataOption('#00ff00'),
	new weTagDataOption('#0000ff'),
	new weTagDataOption('#00ffff'),
	new weTagDataOption('#ff00ff'),
	new weTagDataOption('#ffff00'),
	), false, true, '');
$this->Attributes[] = new weTagData_textAttribute('angle', false, '');
$this->Attributes[] = new weTagData_selectAttribute('align', array(new weTagDataOption('random'),
	new weTagDataOption('center'),
	new weTagDataOption('left'),
	new weTagDataOption('right'),
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('valign', array(new weTagDataOption('random'),
	new weTagDataOption('top'),
	new weTagDataOption('middle'),
	new weTagDataOption('bottom'),
	), false, '');
$this->Attributes[] = new weTagData_textAttribute('font', false, '');
$this->Attributes[] = new weTagData_selectorAttribute('fontpath', FILE_TABLE, weTagData_selectorAttribute::FOLDER, false, '', true);

$this->Attributes[] = new weTagData_selectAttribute('case', array(new weTagDataOption('mix'),
	new weTagDataOption('upper'),
	new weTagDataOption('lower'),
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('gif'),
	new weTagDataOption('jpg'),
	new weTagDataOption('png'),
	), false, '');
$this->Attributes[] = new weTagData_textAttribute('stylenumber', false, '');
$this->Attributes[] = new weTagData_textAttribute('alt', false, '');
