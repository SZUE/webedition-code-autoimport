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
//$this->Groups[] = 'if_tags';
$this->Module = 'newsletter';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->DefaultValue = g_l('weTag', '[' . $tagName . '][defaultvalue]', true);

$this->Attributes = [new we_tagData_selectAttribute('plain', we_tagData_selectAttribute::getTrueFalse(), false, 'newsletter')];
