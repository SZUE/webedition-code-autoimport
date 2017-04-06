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
	new we_tagData_textAttribute('name', true, ''),
	new we_tagData_choiceAttribute('type', [new we_tagData_option('textinput'),
		new we_tagData_option('textarea'),
		new we_tagData_option('select'),
		new we_tagData_option('radio'),
		new we_tagData_option('checkbox'),
		new we_tagData_option('country'),
		new we_tagData_option('language'),
		new we_tagData_option('file'),
		], false, true, ''),
	new we_tagData_textAttribute('attribs', false, ''),
];
