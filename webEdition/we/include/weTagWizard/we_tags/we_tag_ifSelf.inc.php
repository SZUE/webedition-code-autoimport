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
	new we_tagData_multiSelectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, 'ID', false, ''),
	new we_tagData_selectAttribute('doc', [new we_tagData_option('top'),
		new we_tagData_option('self'),
		new we_tagData_option('listview'),
		], false, ''),
];
