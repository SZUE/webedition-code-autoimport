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

$this->Attributes[] = new we_tagData_textAttribute('delimiter', false, '');
$this->Attributes[] = new we_tagData_selectAttribute('doc', [new we_tagData_option('self'),
	new we_tagData_option('top'),
	new we_tagData_option('listview'),
	], false, '');
$this->Attributes[] = new we_tagData_selectAttribute('showpath', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_textAttribute('rootdir', false, '');
$this->Attributes[] = new we_tagData_selectAttribute('field', [new we_tagData_option('ID'),
	new we_tagData_option('Path'),
	new we_tagData_option('Title'),
	new we_tagData_option('Description'),
	], false, '');
$this->Attributes[] = new we_tagData_textAttribute('onlyindir', false, '');
$this->Attributes[] = (defined('CATEGORY_TABLE') ? new we_tagData_selectorAttribute('id', CATEGORY_TABLE, '', false, '') : null);
$this->Attributes[] = new we_tagData_textAttribute('separator', false, '');
$this->Attributes[] = new we_tagData_textAttribute('name', false, '');
$this->Attributes[] = new we_tagData_selectAttribute('multiple', we_tagData_selectAttribute::getTrueFalse(), false, '');
