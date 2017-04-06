<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
*/
$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
if(defined('CUSTOMER_TABLE')){

	$this->Attributes = [
		new we_tagData_selectAttribute('persistentlogins', we_tagData_selectAttribute::getTrueFalse(), false, ''),
		new we_tagData_selectAttribute('onlinemonitor', we_tagData_selectAttribute::getTrueFalse(), false, ''),
		new we_tagData_sqlColAttribute('monitorgroupfield', CUSTOMER_TABLE, false, [], ''),
		new we_tagData_selectAttribute('monitordoc', [new we_tagData_option('self'),
			new we_tagData_option('top'),
			], false, ''),
	];
}
