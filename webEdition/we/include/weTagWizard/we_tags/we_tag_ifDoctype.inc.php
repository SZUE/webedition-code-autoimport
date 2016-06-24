<?php

//NOTE you are inside the constructor of weTagData.class.php

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
$this->Attributes[] = new weTagData_choiceAttribute('doctypes', $docTypes, false, true, '');

$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('top'),
	new weTagDataOption('self'),
	new weTagDataOption('listview'),
	), false, '');
