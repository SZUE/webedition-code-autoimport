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
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new we_tagData_choiceAttribute('type', [new we_tagData_option('block'),
	new we_tagData_option('linklist'),
	new we_tagData_option('listdir'),
	new we_tagData_option('listview'),
	], true, false, '');
$this->Attributes[] = new we_tagData_choiceAttribute('position', [new we_tagData_option('first'),
	new we_tagData_option('last'),
	new we_tagData_option('odd'),
	new we_tagData_option('even'),
	], true, true, '');
$this->Attributes[] = new we_tagData_textAttribute('reference', false, '');
$this->Attributes[] = new we_tagData_selectAttribute('operator', [new we_tagData_option('equal'),
	new we_tagData_option('less'),
	new we_tagData_option('less|equal'),
	new we_tagData_option('greater'),
	new we_tagData_option('greater|equal'),
	new we_tagData_option('every'),
	], false, '');
