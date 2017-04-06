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

$this->Attributes[] = new we_tagData_choiceAttribute('type', [new we_tagData_option('button'),
	new we_tagData_option('checkbox'),
 ], true, false, '');
$this->Attributes[] = new we_tagData_choiceAttribute('name', [new we_tagData_option('delete'),
	new we_tagData_option('makeSameDoc'),
	new we_tagData_option('publish'),
	new we_tagData_option('save'),
	new we_tagData_option('unpublish'),
	new we_tagData_option('workflow', false, 'workflow')], true, false, '');
$this->Attributes[] = new we_tagData_choiceAttribute('hide', we_tagData_selectAttribute::getTrueFalse(), false, false, '');
$this->Attributes[] = new we_tagData_choiceAttribute('readonly', we_tagData_selectAttribute::getTrueFalse(), false, false, '');
$this->Attributes[] = new we_tagData_choiceAttribute('checked', we_tagData_selectAttribute::getTrueFalse(), false, false, '');
