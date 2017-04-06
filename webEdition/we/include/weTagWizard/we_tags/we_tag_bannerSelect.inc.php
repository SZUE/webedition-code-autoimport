<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
*/
$this->Module = 'banner';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new we_tagData_textAttribute('name', true, '');
$this->Attributes[] = new we_tagData_selectAttribute('showpath', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_textAttribute('rootdir', false, '');
$this->Attributes[] = new we_tagData_textAttribute('firstentry', false, '');
$this->Attributes[] = new we_tagData_selectAttribute('submitonchange', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_selectAttribute('customer', we_tagData_selectAttribute::getTrueFalse(), false, 'customer');
