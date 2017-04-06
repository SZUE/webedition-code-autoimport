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
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'voting';

$this->Attributes = [
	new we_tagData_textAttribute('firstentry', false, ''),
	new we_tagData_selectAttribute('submitonchange', [new we_tagData_option('false'),
		new we_tagData_option('true'),
	 ], false, ''),
	new we_tagData_selectAttribute('reload', [new we_tagData_option('false'),
		new we_tagData_option('true'),
	 ], false, ''),
	new we_tagData_textAttribute('parentid', false, ''),
];
