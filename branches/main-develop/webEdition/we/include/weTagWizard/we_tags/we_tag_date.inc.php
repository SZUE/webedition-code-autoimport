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
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new weTagData_selectAttribute('type', [
		new weTagDataOption('js'),
		new weTagDataOption('php'),
		], false, ''),
	new weTagData_textAttribute('format', false, ''),
	new weTagData_textAttribute('outputlanguage', false, ''),
];
