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
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes[] = new we_tagData_textAttribute('shopname', true, '');
$this->Attributes[] = new we_tagData_selectAttribute('floatquantities', we_tagData_selectAttribute::getTrueFalse(), false, '');
