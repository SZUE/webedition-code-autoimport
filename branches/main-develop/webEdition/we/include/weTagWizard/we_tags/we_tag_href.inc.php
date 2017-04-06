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
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [new we_tagData_textAttribute('name', true, ''),
	new we_tagData_textAttribute('size', false, ''),
	new we_tagData_selectAttribute('type', [new we_tagData_option('all'),
		new we_tagData_option('int'),
		new we_tagData_option('ext'),
		], false, ''),
	new we_tagData_selectAttribute('include', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_selectAttribute('file', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_selectAttribute('directory', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_selectAttribute('reload', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_selectAttribute('hidedirindex', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_textAttribute('user', false, 'users'),
	new we_tagData_textAttribute('rootdir', false, ''),
	new we_tagData_selectorAttribute('startid', FILE_TABLE, we_tagData_selectorAttribute::FOLDER, false, ''),
	new we_tagData_selectAttribute('cfilter', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_selectAttribute('only', [new we_tagData_option('id'), new we_tagData_option('path')], false, ''),
];
