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

$this->Attributes = [new we_tagData_textAttribute('index', false, ''),
	new we_tagData_textAttribute('separator', false, ''),
	new we_tagData_textAttribute('home', false, ''),
	new we_tagData_selectAttribute('hidehome', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_textAttribute('field', false, ''),
	new we_tagData_textAttribute('dirfield', false, ''),
	new we_tagData_selectAttribute('fieldforfolder', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_selectAttribute('doc', [new we_tagData_option('top'),
		new we_tagData_option('self'),
		], false, ''),
	new we_tagData_textAttribute('max', false, ''),
	new we_tagData_textAttribute('style'),
	new we_tagData_textAttribute('class'),
];
