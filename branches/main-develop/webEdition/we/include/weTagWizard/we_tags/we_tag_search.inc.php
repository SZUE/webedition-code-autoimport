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
	new we_tagData_selectAttribute('type', [new we_tagData_option('textinput'),
		new we_tagData_option('textarea'),
		new we_tagData_option('print'),
	 ], false, ''),
	new we_tagData_textAttribute('name', false, ''),
	new we_tagData_textAttribute('value', false, ''),
	new we_tagData_textAttribute('size', false, ''),
	new we_tagData_textAttribute('maxlength', false, ''),
	new we_tagData_textAttribute('cols', false, ''),
	new we_tagData_textAttribute('rows', false, ''),
	new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, ''),
];
