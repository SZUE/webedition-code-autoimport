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
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes[] = new weTagData_textAttribute('percent', true, '');
$this->Attributes[] = new weTagData_choiceAttribute('num_format', [new weTagDataOption('german'),
	new weTagDataOption('french'),
	new weTagDataOption('english'),
	new weTagDataOption('swiss'),
 ], false, false, '');
