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
	new weTagData_multiSelectorAttribute('categories', CATEGORY_TABLE, '', 'Path', false, ''),
	new weTagData_multiSelectorAttribute('categoryids', CATEGORY_TABLE, '', 'ID', false, ''),
	new weTagData_selectAttribute('doc', [new weTagDataOption('top'),
		new weTagDataOption('self'),
		new weTagDataOption('listview'),
		], false, ''),
	new weTagData_selectAttribute('parent', weTagData_selectAttribute::getTrueFalse(), false, ''),
];
