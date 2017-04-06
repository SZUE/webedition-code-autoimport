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
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new we_tagData_textAttribute('id', true, ''),
	new we_tagData_textAttribute('show', false, ''),
	new we_tagData_selectAttribute('doc', [new we_tagData_option('top'),
		new we_tagData_option('self'),
	 ], false, ''),
];
