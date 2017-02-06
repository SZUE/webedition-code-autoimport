<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
if(defined('CUSTOMER_TABLE')){
	$this->Attributes[] = new weTagData_selectAttribute('persistentlogins', weTagData_selectAttribute::getTrueFalse(), false, '');
	$this->Attributes[] = new weTagData_selectAttribute('onlinemonitor', weTagData_selectAttribute::getTrueFalse(), false, '');
	$this->Attributes[] = new weTagData_sqlColAttribute('monitorgroupfield', CUSTOMER_TABLE, false, array(), '');
	$this->Attributes[] = new weTagData_selectAttribute('monitordoc', array(new weTagDataOption('self'),
		new weTagDataOption('top'),
		), false, '');
}
