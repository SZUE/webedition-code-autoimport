<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_choiceAttribute('type', array(new weTagDataOption('block'),
	new weTagDataOption('linklist'),
	new weTagDataOption('listdir'),
	new weTagDataOption('listview'),
	), true, false, '');
$this->Attributes[] = new weTagData_choiceAttribute('position', array(
	new weTagDataOption('first'),
	new weTagDataOption('last'),
	new weTagDataOption('odd'),
	new weTagDataOption('even'),
	), true, true, '');
$this->Attributes[] = new weTagData_textAttribute('reference', false, '');
$this->Attributes[] = new weTagData_selectAttribute('operator', array(
	new weTagDataOption('equal'),
	new weTagDataOption('less'),
	new weTagDataOption('less|equal'),
	new weTagDataOption('greater'),
	new weTagDataOption('greater|equal'),
	new weTagDataOption('every'),
	), false, '');
