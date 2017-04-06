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

$this->Attributes[] = new we_tagData_textAttribute('domain', true, '');
$this->Attributes[] = new we_tagData_selectAttribute('matchType', [new we_tagData_option('exact'),
	new we_tagData_option('contains'),
	new we_tagData_option('front'),
	new we_tagData_option('back'),
	], false, '');
