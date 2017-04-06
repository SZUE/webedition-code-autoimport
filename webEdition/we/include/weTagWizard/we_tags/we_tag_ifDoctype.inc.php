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

$db = new DB_WE();
$db->query('SELECT DocType FROM ' . DOC_TYPES_TABLE);
$docTypes = [];
while($db->next_record()){
	$docTypes[] = new we_tagData_option($db->f('DocType'));
}
$this->Attributes = [
	new we_tagData_choiceAttribute('doctypes', $docTypes, false, true, ''),
	new we_tagData_selectAttribute('doc', [new we_tagData_option('top'),
		new we_tagData_option('self'),
		new we_tagData_option('listview'),
		], false, ''),
];
