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

$this->Attributes[] = new we_tagData_textAttribute('size', false, '');
$this->Attributes[] = new we_tagData_textAttribute('maxlength', false, '');
$this->Attributes[] = new we_tagData_textAttribute('value', false, '');
$this->Attributes[] = new we_tagData_textAttribute('class', false, '');
$this->Attributes[] = new we_tagData_textAttribute('style', false, '');
$this->Attributes[] = new we_tagData_textAttribute('onchange', false, '');
$this->Attributes[] = new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, '');
