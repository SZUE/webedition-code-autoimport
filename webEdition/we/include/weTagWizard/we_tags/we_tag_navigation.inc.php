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
$this->Groups[] = 'navigation_tags';
$this->Module = 'navigation';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new we_tagData_textAttribute('navigationname', false, '');
if(defined('NAVIGATION_TABLE')){
	$this->Attributes[] = new we_tagData_selectorAttribute('parentid', NAVIGATION_TABLE, 'weNavigation', false, '');
	$this->Attributes[] = new we_tagData_selectorAttribute('id', NAVIGATION_TABLE, 'weNavigation', false, '');
}
