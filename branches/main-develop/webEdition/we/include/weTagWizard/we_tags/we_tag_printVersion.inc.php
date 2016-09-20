<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	(defined('TEMPLATES_TABLE') ? new weTagData_selectorAttribute('tid', TEMPLATES_TABLE, 'text/weTmpl', true, '') : null),
	new weTagData_choiceAttribute('target', [new weTagDataOption('_top'),
		new weTagDataOption('_parent'),
		new weTagDataOption('_self'),
		new weTagDataOption('_blank'),
		], false, false, ''),
	new weTagData_selectAttribute('link', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_selectAttribute('doc', [new weTagDataOption('top'),
		new weTagDataOption('self'),
		], false, ''),
	(defined('FILE_TABLE') ? new weTagData_selectorAttribute('triggerid', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '') : null),
];

