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

$this->Attributes = [
	new weTagData_textAttribute('width', true, ''),
	new weTagData_textAttribute('height', true, ''),
	new weTagData_textAttribute('maxlength', false, ''),
	new weTagData_selectAttribute('subset', [new weTagDataOption('alphanum'),
		new weTagDataOption('alpha'),
		new weTagDataOption('num'),
		], false, ''),
	new weTagData_textAttribute('skip', false, ''),
	new weTagData_choiceAttribute('fontcolor', [new weTagDataOption('#000000'),
		new weTagDataOption('#ffffff'),
		new weTagDataOption('#ff0000'),
		new weTagDataOption('#00ff00'),
		new weTagDataOption('#0000ff'),
		new weTagDataOption('#ffff00'),
		new weTagDataOption('#ff00ff'),
		new weTagDataOption('#00ffff'),
		], false, true, ''),
	new weTagData_textAttribute('fontsize', false, ''),
	new weTagData_choiceAttribute('bgcolor', [new weTagDataOption('#ffffff'),
		new weTagDataOption('#cccccc'),
		new weTagDataOption('#888888'),
		], false, false, ''),
	new weTagData_selectAttribute('transparent', [new weTagDataOption('false'),
		new weTagDataOption('true'),
		], false, ''),
	new weTagData_choiceAttribute('style', [new weTagDataOption('strikeout'),
		new weTagDataOption('fullcircle'),
		new weTagDataOption('fullrectangle'),
		new weTagDataOption('outlinecircle'),
		new weTagDataOption('outlinerectangle'),
		], false, true, ''),
	new weTagData_choiceAttribute('stylecolor', [new weTagDataOption('#cccccc'),
		new weTagDataOption('#ff0000'),
		new weTagDataOption('#00ff00'),
		new weTagDataOption('#0000ff'),
		new weTagDataOption('#00ffff'),
		new weTagDataOption('#ff00ff'),
		new weTagDataOption('#ffff00'),
		], false, true, ''),
	new weTagData_textAttribute('angle', false, ''),
	new weTagData_selectAttribute('align', [new weTagDataOption('random'),
		new weTagDataOption('center'),
		new weTagDataOption('left'),
		new weTagDataOption('right'),
		], false, ''),
	new weTagData_selectAttribute('valign', [new weTagDataOption('random'),
		new weTagDataOption('top'),
		new weTagDataOption('middle'),
		new weTagDataOption('bottom'),
		], false, ''),
	new weTagData_textAttribute('font', false, ''),
	new weTagData_selectorAttribute('fontpath', FILE_TABLE, weTagData_selectorAttribute::FOLDER, false, '', true),
	new weTagData_selectAttribute('case', [new weTagDataOption('mix'),
		new weTagDataOption('upper'),
		new weTagDataOption('lower'),
		], false, ''),
	new weTagData_selectAttribute('type', [new weTagDataOption('gif'),
		new weTagDataOption('jpg'),
		new weTagDataOption('png'),
		], false, ''),
	new weTagData_textAttribute('stylenumber', false, ''),
	new weTagData_textAttribute('alt', false, ''),
];
