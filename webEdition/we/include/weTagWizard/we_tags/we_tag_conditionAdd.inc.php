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
$this->Module = 'object';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new weTagData_textAttribute('field', true, ''),
	new weTagData_textAttribute('value', false, ''),
	new weTagData_choiceAttribute('compare', [new weTagDataOption('='),
		new weTagDataOption('!='),
		new weTagDataOption('&lt;'),
		new weTagDataOption('&gt;'),
		new weTagDataOption('&lt;='),
		new weTagDataOption('&gt;='),
		new weTagDataOption('LIKE'),
		], false, false, ''),
	new weTagData_textAttribute('var', false, ''),
	new weTagData_selectAttribute('type', [new weTagDataOption('global'),
		new weTagDataOption('request'),
		new weTagDataOption('sessionfield'),
		new weTagDataOption('document'),
		new weTagDataOption('now'),
		], false, ''),
	new weTagData_selectAttribute('property', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_selectAttribute('doc', [new weTagDataOption('self'),
		new weTagDataOption('top'),
		], false, ''),
	new weTagData_selectAttribute('exactmatch', [new weTagDataOption('false'),
		new weTagDataOption('true'),
		], false, ''),
];
