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
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	(defined('TEMPLATES_TABLE') ? new we_tagData_selectorAttribute('tid', TEMPLATES_TABLE, 'text/weTmpl', true, '') : null),
	new we_tagData_choiceAttribute('target', [new we_tagData_option('_top'),
		new we_tagData_option('_parent'),
		new we_tagData_option('_self'),
		new we_tagData_option('_blank'),
		], false, false, ''),
	new we_tagData_selectAttribute('link', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_selectAttribute('doc', [new we_tagData_option('top'),
		new we_tagData_option('self'),
		], false, ''),
	(defined('FILE_TABLE') ? new we_tagData_selectorAttribute('triggerid', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '') : null),
];

