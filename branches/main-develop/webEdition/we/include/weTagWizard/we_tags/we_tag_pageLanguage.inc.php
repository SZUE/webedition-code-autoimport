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

$this->Attributes = [new we_tagData_selectAttribute('type', [new we_tagData_option('complete'),
		new we_tagData_option('language'),
		new we_tagData_option('language_name'),
		new we_tagData_option('country'),
		new we_tagData_option('country_name'),
		], false, ''),
	new we_tagData_selectAttribute('case', [new we_tagData_option('unchanged'),
		new we_tagData_option('uppercase'),
		new we_tagData_option('lowercase'),
		], false, ''),
	new we_tagData_selectAttribute('doc', [new we_tagData_option('self'),
		new we_tagData_option('top'),
		], false, ''),
];
