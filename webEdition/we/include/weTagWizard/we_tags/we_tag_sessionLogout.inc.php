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
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	(defined('FILE_TABLE') ? new we_tagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, true, '') : null),
	new we_tagData_choiceAttribute('target', [new we_tagData_option('_top'),
		new we_tagData_option('_parent'),
		new we_tagData_option('_self'),
		new we_tagData_option('_blank'),
		], false, false, ''),
	new we_tagData_textAttribute('class', false, ''),
	new we_tagData_textAttribute('style', false, ''),
];
