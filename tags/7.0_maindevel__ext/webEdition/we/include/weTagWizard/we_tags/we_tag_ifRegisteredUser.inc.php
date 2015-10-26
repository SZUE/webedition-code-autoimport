<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
if(defined('CUSTOMER_TABLE')){
	$this->Attributes[] = new weTagData_sqlColAttribute('permission', CUSTOMER_TABLE, false, array(), '');
	$this->Attributes[] = new weTagData_textAttribute('match', false, '');
	$this->Attributes[] = new weTagData_textAttribute('userid', false, '');
	$this->Attributes[] = new weTagData_selectAttribute('cfilter', weTagData_selectAttribute::getTrueFalse(), false, '');
	$this->Attributes[] = new weTagData_selectAttribute('allowNoFilter', weTagData_selectAttribute::getTrueFalse(), false, '');
	$this->Attributes[] = new weTagData_selectAttribute('matchType', array(new weTagDataOption('one'),
		new weTagDataOption('contains'),
		new weTagDataOption('exact'),
		), false, '');
}