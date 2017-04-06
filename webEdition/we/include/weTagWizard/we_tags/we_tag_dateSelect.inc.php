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
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new we_tagData_textAttribute('name', true, '');
$this->Attributes[] = new we_tagData_textAttribute('class', false, '');
$this->Attributes[] = new we_tagData_textAttribute('style', false, '');
$this->Attributes[] = new we_tagData_selectAttribute('submitonchange', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_textAttribute('start', false, '');
$this->Attributes[] = new we_tagData_textAttribute('end', false, '');
