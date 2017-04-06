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

$this->Attributes[] = new we_tagData_textAttribute('name', false, '');
$this->Attributes[] = new we_tagData_textAttribute('id', false, '');

$this->Attributes[] = new we_tagData_textAttribute('condition', false, '');
