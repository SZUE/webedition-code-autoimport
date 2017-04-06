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
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

if(defined('FILE_TABLE')){
	$this->Attributes[] = new we_tagData_selectorAttribute('id', FILE_TABLE, we_tagData_selectorAttribute::FOLDER, false, '');
}
$this->Attributes[] = new we_tagData_textAttribute('index', false, '');
$this->Attributes[] = new we_tagData_textAttribute('field', false, '');
$this->Attributes[] = new we_tagData_textAttribute('dirfield', false, '');
$this->Attributes[] = new we_tagData_textAttribute('order', false, '');
$this->Attributes[] = new we_tagData_selectAttribute('desc', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_selectAttribute('searchable', we_tagData_selectAttribute::getTrueFalse(), false, '');
