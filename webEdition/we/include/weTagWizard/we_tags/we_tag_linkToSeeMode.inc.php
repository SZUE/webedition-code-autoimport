<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [

	new weTagData_textAttribute('value', true, ''),
	new weTagData_selectAttribute('doc', [new weTagDataOption('top'),
		new weTagDataOption('self'),
		], false, ''),
	(defined('FILE_TABLE') ? new weTagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '') : null),
	(defined('OBJECT_FILES_TABLE') ? new weTagData_selectorAttribute('oid', OBJECT_FILES_TABLE, 'objectFile', false, '') : null),
	new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, ''),
	(defined('CUSTOMER_TABLE') ? new weTagData_sqlColAttribute('permission', CUSTOMER_TABLE, false, [], '') : null),
];

