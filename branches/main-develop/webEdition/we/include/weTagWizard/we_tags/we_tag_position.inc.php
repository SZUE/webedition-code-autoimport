<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_choiceAttribute('type', array(new weTagDataOption('block'),
	new weTagDataOption('linklist'),
	new weTagDataOption('listdir'),
	new weTagDataOption('listview'),
	), true, false, '');
$this->Attributes[] = new weTagData_choiceAttribute('format', array(new weTagDataOption('1'),
	new weTagDataOption('a'),
	new weTagDataOption('A'),
	), false, false, '');
$this->Attributes[] = new weTagData_textAttribute('reference', false, '');
