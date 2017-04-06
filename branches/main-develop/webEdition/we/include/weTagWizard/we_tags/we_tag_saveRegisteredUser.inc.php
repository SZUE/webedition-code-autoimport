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
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new we_tagData_textAttribute('userexists', false, '', true);
$this->Attributes[] = new we_tagData_textAttribute('userempty', false, '', true);
$this->Attributes[] = new we_tagData_textAttribute('passempty', false, '', true);
$this->Attributes[] = new we_tagData_selectAttribute('register', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_textAttribute('allowed', false, '');
$this->Attributes[] = new we_tagData_textAttribute('protected', false, '');
$this->Attributes[] = new we_tagData_selectAttribute('changesessiondata', we_tagData_selectAttribute::getTrueFalse(), false, '');
