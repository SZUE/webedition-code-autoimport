<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

if(defined('FILE_TABLE')){
	$this->Attributes[] = new weTagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, true, '');
}
$this->Attributes[] = new weTagData_choiceAttribute('target', array(new weTagDataOption('_top'),
	new weTagDataOption('_parent'),
	new weTagDataOption('_self'),
	new weTagDataOption('_blank'),
	), false, false, '');
$this->Attributes[] = new weTagData_textAttribute('class', false, '');
$this->Attributes[] = new weTagData_textAttribute('style', false, '');
