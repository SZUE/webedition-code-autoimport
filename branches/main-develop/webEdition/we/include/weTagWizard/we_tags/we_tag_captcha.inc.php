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
	new we_tagData_textAttribute('width', true, ''),
	new we_tagData_textAttribute('height', true, ''),
	new we_tagData_textAttribute('maxlength', false, ''),
	new we_tagData_selectAttribute('subset', [new we_tagData_option('alphanum'),
		new we_tagData_option('alpha'),
		new we_tagData_option('num'),
		], false, ''),
	new we_tagData_textAttribute('skip', false, ''),
	new we_tagData_choiceAttribute('fontcolor', [new we_tagData_option('#000000'),
		new we_tagData_option('#ffffff'),
		new we_tagData_option('#ff0000'),
		new we_tagData_option('#00ff00'),
		new we_tagData_option('#0000ff'),
		new we_tagData_option('#ffff00'),
		new we_tagData_option('#ff00ff'),
		new we_tagData_option('#00ffff'),
		], false, true, ''),
	new we_tagData_textAttribute('fontsize', false, ''),
	new we_tagData_choiceAttribute('bgcolor', [new we_tagData_option('#ffffff'),
		new we_tagData_option('#cccccc'),
		new we_tagData_option('#888888'),
		], false, false, ''),
	new we_tagData_selectAttribute('transparent', [new we_tagData_option('false'),
		new we_tagData_option('true'),
		], false, ''),
	new we_tagData_choiceAttribute('style', [new we_tagData_option('strikeout'),
		new we_tagData_option('fullcircle'),
		new we_tagData_option('fullrectangle'),
		new we_tagData_option('outlinecircle'),
		new we_tagData_option('outlinerectangle'),
		], false, true, ''),
	new we_tagData_choiceAttribute('stylecolor', [new we_tagData_option('#cccccc'),
		new we_tagData_option('#ff0000'),
		new we_tagData_option('#00ff00'),
		new we_tagData_option('#0000ff'),
		new we_tagData_option('#00ffff'),
		new we_tagData_option('#ff00ff'),
		new we_tagData_option('#ffff00'),
		], false, true, ''),
	new we_tagData_textAttribute('angle', false, ''),
	new we_tagData_selectAttribute('align', [new we_tagData_option('random'),
		new we_tagData_option('center'),
		new we_tagData_option('left'),
		new we_tagData_option('right'),
		], false, ''),
	new we_tagData_selectAttribute('valign', [new we_tagData_option('random'),
		new we_tagData_option('top'),
		new we_tagData_option('middle'),
		new we_tagData_option('bottom'),
		], false, ''),
	new we_tagData_textAttribute('font', false, ''),
	new we_tagData_selectorAttribute('fontpath', FILE_TABLE, we_tagData_selectorAttribute::FOLDER, false, '', true),
	new we_tagData_selectAttribute('case', [new we_tagData_option('mix'),
		new we_tagData_option('upper'),
		new we_tagData_option('lower'),
		], false, ''),
	new we_tagData_selectAttribute('type', [new we_tagData_option('gif'),
		new we_tagData_option('jpg'),
		new we_tagData_option('png'),
		], false, ''),
	new we_tagData_textAttribute('stylenumber', false, ''),
	new we_tagData_textAttribute('alt', false, ''),
];
