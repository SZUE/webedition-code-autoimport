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
$this->Groups[] = 'if_tags';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes = [
	new weTagData_textAttribute('name', true, ''),
	new weTagData_selectAttribute('reference', [new weTagDataOption('article'),
		new weTagDataOption('cart'),
		], true, ''),
	new weTagData_textAttribute('shopname', true, ''),
];
