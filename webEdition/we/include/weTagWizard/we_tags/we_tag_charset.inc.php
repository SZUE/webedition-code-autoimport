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

$this->Attributes = [ new we_tagData_choiceAttribute('defined', [new we_tagData_option('UTF-8'),
		new we_tagData_option('ISO-8859-1'),
		new we_tagData_option('ISO-8859-2'),
		new we_tagData_option('ISO-8859-3'),
		new we_tagData_option('ISO-8859-4'),
		new we_tagData_option('ISO-8859-5'),
		new we_tagData_option('ISO-8859-6'),
		new we_tagData_option('ISO-8859-7'),
		new we_tagData_option('ISO-8859-8'),
		new we_tagData_option('ISO-8859-9'),
		new we_tagData_option('ISO-8859-10'),
		new we_tagData_option('ISO-8859-11'),
		new we_tagData_option('ISO-8859-13'),
		new we_tagData_option('ISO-8859-14'),
		new we_tagData_option('ISO-8859-15'),
		new we_tagData_option('Windows-1251'),
		new we_tagData_option('Windows-1252'),
		], false, true, ''),
	new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, '')
];
