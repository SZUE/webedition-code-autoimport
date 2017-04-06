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

$this->Attributes = [ new we_tagData_textAttribute('name', false, ''),
	new we_tagData_selectAttribute('type', [new we_tagData_option('request'),], false, ''),
	new we_tagData_selectAttribute('showpath', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_textAttribute('rootdir', false, ''),
	new we_tagData_textAttribute('firstentry', false, ''),
	new we_tagData_selectAttribute('multiple', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_textAttribute('indent', false, ''),
];
