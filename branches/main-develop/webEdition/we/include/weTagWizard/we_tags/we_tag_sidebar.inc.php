<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
*/
$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->DefaultValue = g_l('weTag', '[' . $tagName . '][defaultvalue]', true);

$this->Attributes = [
	(defined('FILE_TABLE') ? new we_tagData_selectorAttribute('id', FILE_TABLE, '', false, '') : null),
	new we_tagData_textAttribute('file', false, ''),
	new we_tagData_textAttribute('url', false, ''),
	new we_tagData_choiceAttribute('width', [new we_tagData_option('100'),
		new we_tagData_option('150'),
		new we_tagData_option('200'),
		new we_tagData_option('250'),
		new we_tagData_option('300'),
		new we_tagData_option('350'),
		new we_tagData_option('400'),
		], false, true, ''),
];
