<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = (defined('TEMPLATES_TABLE') ? new weTagData_selectorAttribute('id', TEMPLATES_TABLE, '', false, '') : null);
$this->Attributes[] = //new weTagData_textAttribute('path', false, '');
	(defined('TEMPLATES_TABLE') ? new weTagData_selectorAttribute('path', TEMPLATES_TABLE, 'text/weTmpl', false, '', true) : null);
$this->Attributes[] = (defined('TEMPLATES_TABLE') ? new weTagData_selectorAttribute('parentid', TEMPLATES_TABLE, weTagData_selectorAttribute::FOLDER, false, '') : null);
