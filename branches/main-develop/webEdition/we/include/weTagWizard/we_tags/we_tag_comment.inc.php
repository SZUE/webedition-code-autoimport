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

$this->Attributes[] = new we_tagData_choiceAttribute('type', [new we_tagData_option('xml'),
	new we_tagData_option('html'),
	new we_tagData_option('js'),
	new we_tagData_option('php'),
	], false, false, '');
