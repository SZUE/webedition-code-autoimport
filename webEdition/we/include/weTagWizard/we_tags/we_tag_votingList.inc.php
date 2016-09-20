<?php
$this->NeedsEndTag = true;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'voting';
$this->DefaultValue = '<we:repeat>
</we:repeat>';

$this->Attributes = [
	new weTagData_textAttribute('name', true, ''),
	new weTagData_textAttribute('groupid', false, ''),
	new weTagData_textAttribute('version', false, ''),
	new weTagData_textAttribute('rows', false, ''),
	new weTagData_textAttribute('offset', false, ''),
	new weTagData_selectAttribute('desc', [new weTagDataOption('true'),
		], false, ''),
	new weTagData_textAttribute('order', false, ''),
	new weTagData_selectAttribute('subgroup', weTagData_selectAttribute::getTrueFalse(), false, ''),
];
