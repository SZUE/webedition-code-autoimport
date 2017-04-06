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
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes = [
	new we_tagData_textAttribute('name', true, ''),
	new we_tagData_selectAttribute('reference', [new we_tagData_option('article'),
		new we_tagData_option('cart'),
		], true, ''),
	new we_tagData_textAttribute('shopname', true, ''),
	new we_tagData_textAttribute('match', true, ''),
	new we_tagData_selectAttribute('operator', [new we_tagData_option('equal'),
		new we_tagData_option('less'),
		new we_tagData_option('less|equal'),
		new we_tagData_option('greater'),
		new we_tagData_option('greater|equal'),
		new we_tagData_option('contains'),
		], false, ''),
];
