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
	new we_tagData_textAttribute('field', true, ''),
	new we_tagData_textAttribute('value', false, ''),
	new we_tagData_choiceAttribute('compare', [new we_tagData_option('='),
		new we_tagData_option('!='),
		new we_tagData_option('&lt;'),
		new we_tagData_option('&gt;'),
		new we_tagData_option('&lt;='),
		new we_tagData_option('&gt;='),
		new we_tagData_option('LIKE'),
		], false, false, ''),
	new we_tagData_textAttribute('var', false, ''),
	new we_tagData_selectAttribute('type', [new we_tagData_option('global'),
		new we_tagData_option('request'),
		new we_tagData_option('sessionfield'),
		new we_tagData_option('document'),
		new we_tagData_option('now'),
		], false, ''),
	new we_tagData_selectAttribute('property', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_selectAttribute('doc', [new we_tagData_option('self'),
		new we_tagData_option('top'),
		], false, ''),
	new we_tagData_selectAttribute('exactmatch', [new we_tagData_option('false'),
		new we_tagData_option('true'),
		], false, ''),
];
