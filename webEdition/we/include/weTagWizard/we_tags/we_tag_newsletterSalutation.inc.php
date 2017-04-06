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
//$this->Groups[] = 'if_tags';
$this->Module = 'newsletter';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new we_tagData_selectAttribute('type', [new we_tagData_option('email'), new we_tagData_option('salutation', false, 'newsletter'), new we_tagData_option('title', false, 'newsletter'), new we_tagData_option('firstname', false, 'newsletter'), new we_tagData_option('lastname', false, 'newsletter')], false, '')
];
