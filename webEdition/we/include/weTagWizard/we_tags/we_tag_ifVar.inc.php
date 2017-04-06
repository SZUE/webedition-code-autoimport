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
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new we_tagData_textAttribute('name', true, ''),
	new we_tagData_textAttribute('match', true, ''),
	new we_tagData_selectAttribute('type', [new we_tagData_option('global'),
		new we_tagData_option('request'),
		new we_tagData_option('post'),
		new we_tagData_option('get'),
		new we_tagData_option('document'),
		new we_tagData_option('property'),
		new we_tagData_option('session'),
		new we_tagData_option('sessionfield'),
		], false, ''),
	new we_tagData_selectAttribute('doc', [new we_tagData_option('self'),
		new we_tagData_option('top'),
		], false, ''),
	new we_tagData_selectAttribute('operator', [new we_tagData_option('equal'),
		new we_tagData_option('less'),
		new we_tagData_option('less|equal'),
		new we_tagData_option('greater'),
		new we_tagData_option('greater|equal'),
		new we_tagData_option('contains'),
		new we_tagData_option('isin'),
		], false, ''),
];
