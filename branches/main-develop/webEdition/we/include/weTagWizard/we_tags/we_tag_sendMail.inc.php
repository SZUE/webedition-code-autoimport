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

$this->Attributes = [
	new weTagData_textAttribute('id', false, ''),
	new weTagData_textAttribute('subject', false, ''),
	new weTagData_textAttribute('recipient', true, ''),
	new weTagData_textAttribute('recipientCC', false, ''),
	new weTagData_textAttribute('recipientBCC', false, ''),
	new weTagData_textAttribute('from', true, ''),
	new weTagData_textAttribute('reply', false, ''),
	new weTagData_selectAttribute('mimetype', [new weTagDataOption('text/plain'),
		new weTagDataOption('text/html'),
	 ], false, ''),
	new weTagData_textAttribute('charset', false, ''),
	new weTagData_selectAttribute('includeimages', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_selectAttribute('usebasehref', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_selectAttribute('useformmailLog', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_selectAttribute('useformmailBlock', weTagData_selectAttribute::getTrueFalse(), false, ''),
];
