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
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new we_tagData_multiSelectorAttribute('categories', CATEGORY_TABLE, '', 'Path', false, ''),
	new we_tagData_multiSelectorAttribute('categoryids', CATEGORY_TABLE, '', 'ID', false, ''),
	new we_tagData_selectAttribute('doc', [new we_tagData_option('top'),
		new we_tagData_option('self'),
		new we_tagData_option('listview'),
		], false, ''),
	new we_tagData_selectAttribute('parent', we_tagData_selectAttribute::getTrueFalse(), false, ''),
];
