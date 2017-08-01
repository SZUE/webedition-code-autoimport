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

	new we_tagData_textAttribute('value', true, ''),
	new we_tagData_selectAttribute('doc', [new we_tagData_option('top'),
		new we_tagData_option('self'),
		], false, ''),
	(defined('FILE_TABLE') ? new we_tagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '') : null),
	(defined('OBJECT_FILES_TABLE') ? new we_tagData_selectorAttribute('oid', OBJECT_FILES_TABLE, we_base_ContentTypes::OBJECT_FILE, false, '') : null),
	new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	(defined('CUSTOMER_TABLE') ? new we_tagData_sqlColAttribute('permission', CUSTOMER_TABLE, false, [], '') : null),
];

