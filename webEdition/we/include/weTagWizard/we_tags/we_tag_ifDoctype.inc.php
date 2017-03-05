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
	$docTypes[] = new weTagDataOption($db->f('DocType'));
}
$this->Attributes = [
	new weTagData_choiceAttribute('doctypes', $docTypes, false, true, ''),
	new weTagData_selectAttribute('doc', [new weTagDataOption('top'),
		new weTagDataOption('self'),
		new weTagDataOption('listview'),
		], false, ''),
];
