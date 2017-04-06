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

$this->Attributes = [
	(defined('FILE_TABLE') ? new we_tagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '') : null),
	new we_tagData_textAttribute('class', false, ''),
	new we_tagData_textAttribute('style', false, ''),
	new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_selectAttribute('only', [new we_tagData_option('href'),
		new we_tagData_option('id'),
	 ], false, ''),
];
