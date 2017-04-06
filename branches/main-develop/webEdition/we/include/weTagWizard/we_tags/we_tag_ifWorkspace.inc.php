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

$this->Attributes = [//new weTagData_textAttribute('path', false, '');
	(defined('FILE_TABLE') ? new we_tagData_selectorAttribute('path', FILE_TABLE, we_tagData_selectorAttribute::FOLDER, false, '', true) : null),
	new we_tagData_selectAttribute('doc', [new we_tagData_option('top'),
		new we_tagData_option('self'),
		], false, ''),
	(defined('FILE_TABLE') ? new we_tagData_selectorAttribute('id', FILE_TABLE, we_tagData_selectorAttribute::FOLDER, false, '') : null)
];
