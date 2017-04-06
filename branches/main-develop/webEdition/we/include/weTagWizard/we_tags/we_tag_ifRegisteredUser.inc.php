<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
*/
$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
if(defined('CUSTOMER_TABLE')){
	$this->Attributes = [
		new we_tagData_sqlColAttribute('permission', CUSTOMER_TABLE, false, [], ''),
		new we_tagData_textAttribute('match', false, ''),
		new we_tagData_textAttribute('userid', false, ''),
		new we_tagData_selectAttribute('cfilter', we_tagData_selectAttribute::getTrueFalse(), false, ''),
		new we_tagData_selectAttribute('allowNoFilter', we_tagData_selectAttribute::getTrueFalse(), false, ''),
		new we_tagData_selectAttribute('matchType', [new we_tagData_option('one'),
			new we_tagData_option('contains'),
			new we_tagData_option('exact'),
			], false, ''),
	];
}