<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('value', true, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('top'),
	new weTagDataOption('self'),
	), false, '');
if(defined('FILE_TABLE')){
	$this->Attributes[] = new weTagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
}
if(defined('OBJECT_FILES_TABLE')){
	$this->Attributes[] = new weTagData_selectorAttribute('oid', OBJECT_FILES_TABLE, 'objectFile', false, '');
}
$this->Attributes[] = new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, '');
if(defined('CUSTOMER_TABLE')){
	$this->Attributes[] = new weTagData_sqlColAttribute('permission', CUSTOMER_TABLE, false, [], '');
}
