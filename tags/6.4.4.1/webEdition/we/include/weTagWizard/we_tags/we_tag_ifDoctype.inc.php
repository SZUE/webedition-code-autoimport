<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$_db = new DB_WE();
$_db->query('SELECT DocType FROM ' . DOC_TYPES_TABLE);
$docTypes = array();
while($_db->next_record()){
	$docTypes[] = new weTagDataOption($_db->f('DocType'));
}
$this->Attributes[] = new weTagData_choiceAttribute('doctypes', $docTypes, false, true, '');

$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('top'),
	new weTagDataOption('self'),
	new weTagDataOption('listview'),
	), false, '');
