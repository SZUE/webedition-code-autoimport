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
$this->Description = ''; //g_l('weTag', '[' . $tagName . '][description]', true);
$this->Attributes[] = new weTagData_textAttribute('lifetime', true, '');
