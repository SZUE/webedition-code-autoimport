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
//$this->Groups[] = 'input_tags';
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('all'),
	new we_tagData_option('passwordMismatch'),
	new we_tagData_option('required'),
	new we_tagData_option('token'),
	], true, '');

