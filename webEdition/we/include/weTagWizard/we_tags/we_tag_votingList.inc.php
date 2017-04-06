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
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'voting';
$this->DefaultValue = '<we:repeat>
</we:repeat>';

$this->Attributes = [
	new we_tagData_textAttribute('name', true, ''),
	new we_tagData_textAttribute('groupid', false, ''),
	new we_tagData_textAttribute('version', false, ''),
	new we_tagData_textAttribute('rows', false, ''),
	new we_tagData_textAttribute('offset', false, ''),
	new we_tagData_selectAttribute('desc', [new we_tagData_option('true'),
		], false, ''),
	new we_tagData_textAttribute('order', false, ''),
	new we_tagData_selectAttribute('subgroup', we_tagData_selectAttribute::getTrueFalse(), false, ''),
];
