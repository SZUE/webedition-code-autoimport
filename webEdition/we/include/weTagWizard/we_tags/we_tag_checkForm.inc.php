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
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new we_tagData_textAttribute('match', true),
	new we_tagData_selectAttribute('type', [new we_tagData_option('id'), new we_tagData_option('name')], true),
	new we_tagData_textAttribute('mandatory'),
	new we_tagData_textAttribute('email'),
	new we_tagData_textAttribute('password'),
	new we_tagData_textAttribute('onError'),
	new we_tagData_textAttribute('jsIncludePath'),
];
