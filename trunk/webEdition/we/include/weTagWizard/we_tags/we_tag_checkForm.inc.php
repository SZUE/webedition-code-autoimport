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

$this->Attributes[] = new weTagData_textAttribute('match', true);
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('id'), new weTagDataOption('name')), true);
$this->Attributes[] = new weTagData_textAttribute('mandatory');
$this->Attributes[] = new weTagData_textAttribute('email');
$this->Attributes[] = new weTagData_textAttribute('password');
$this->Attributes[] = new weTagData_textAttribute('onError');
$this->Attributes[] = new weTagData_textAttribute('jsIncludePath');
