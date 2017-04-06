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
$this->DefaultValue = '<we:link /><we:postlink><br /></we:postlink>';
$this->Deprecated = true;

$this->Attributes[] = new we_tagData_textAttribute('name', true, '');
$this->Attributes[] = new we_tagData_textAttribute('limit', false, '');
$this->Attributes[] = new we_tagData_selectAttribute('hidedirindex', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_selectAttribute('objectseourls', we_tagData_selectAttribute::getTrueFalse(), false, '');
