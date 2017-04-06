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
	new we_tagData_textAttribute('match', true, ''),
	new we_tagData_selectAttribute('type', [new we_tagData_option('img'),
		new we_tagData_option('flashmovie'),
		new we_tagData_option('binary'),
		new we_tagData_option('href'),
		new we_tagData_option('object'),
		new we_tagData_option('multiobject'),
		new we_tagData_option('calendar'),
		new we_tagData_option('checkbox'),
		new we_tagData_option('int'),
		new we_tagData_option('float'),
		new we_tagData_option('collection'),
		], false, ''),
];
