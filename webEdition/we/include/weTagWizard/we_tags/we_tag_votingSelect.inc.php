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
$this->Module = 'voting';

$this->Attributes = [
	new weTagData_textAttribute('firstentry', false, ''),
	new weTagData_selectAttribute('submitonchange', [new weTagDataOption('false'),
		new weTagDataOption('true'),
	 ], false, ''),
	new weTagData_selectAttribute('reload', [new weTagDataOption('false'),
		new weTagDataOption('true'),
	 ], false, ''),
	new weTagData_textAttribute('parentid', false, ''),
];
