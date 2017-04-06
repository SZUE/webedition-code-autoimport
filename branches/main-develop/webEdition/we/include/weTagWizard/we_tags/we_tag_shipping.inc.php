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
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes = [
	new we_tagData_textAttribute('sum', true, ''),
	new we_tagData_choiceAttribute('num_format', [new we_tagData_option('german'),
		new we_tagData_option('french'),
		new we_tagData_option('english'),
		new we_tagData_option('swiss'),
		], false, false, ''),
	new we_tagData_choiceAttribute('type', [new we_tagData_option('net'),
		new we_tagData_option('gros'),
		new we_tagData_option('vat'),
		], false, false, ''),
];
