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
$this->Groups[] = 'input_tags';
$this->Module = 'collection';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Attributes = [new we_tagData_selectorAttribute('id', VFILE_TABLE, we_base_ContentTypes::COLLECTION, false, ''),
	new we_tagData_textAttribute('name', true, ''),
];
