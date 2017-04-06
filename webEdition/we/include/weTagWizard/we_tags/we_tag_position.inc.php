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
	new we_tagData_choiceAttribute('type', [new we_tagData_option('block'),
		new we_tagData_option('linklist'),
		new we_tagData_option('listdir'),
		new we_tagData_option('listview'),
		], true, false, ''),
	new we_tagData_choiceAttribute('format', [new we_tagData_option('1'),
		new we_tagData_option('a'),
		new we_tagData_option('A'),
		], false, false, ''),
	new we_tagData_textAttribute('reference', false, ''),
];
