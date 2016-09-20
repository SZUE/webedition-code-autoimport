<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [//new weTagData_textAttribute('path', false, '');
	(defined('FILE_TABLE') ? new weTagData_selectorAttribute('path', FILE_TABLE, weTagData_selectorAttribute::FOLDER, false, '', true) : null),
	new weTagData_selectAttribute('doc', [new weTagDataOption('top'),
		new weTagDataOption('self'),
		], false, ''),
	(defined('FILE_TABLE') ? new weTagData_selectorAttribute('id', FILE_TABLE, weTagData_selectorAttribute::FOLDER, false, '') : null)
];
