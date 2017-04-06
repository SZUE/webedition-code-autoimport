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

$this->Attributes = [new we_tagData_selectAttribute('type', [new we_tagData_option('document'),
		new we_tagData_option('object'),
		new we_tagData_option('customer'),
		new we_tagData_option('shop'),
		], false, ''),
	new we_tagData_selectAttribute('onerror', [new we_tagData_option('all'),
		new we_tagData_option('nousername'),
		new we_tagData_option('nopassword'),
		new we_tagData_option('userexists'),
		new we_tagData_option('passwordRule'),
		], false, ''),
	new we_tagData_textAttribute('formname', false, '')
];
