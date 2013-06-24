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
$this->Attributes[] = new weTagData_selectAttribute('to', array(new weTagDataOption('screen'),
	new weTagDataOption('request'),
	new weTagDataOption('post'),
	new weTagDataOption('get'),
	new weTagDataOption('global'),
	new weTagDataOption('session'),
	new weTagDataOption('top'),
	new weTagDataOption('self'),
	new weTagDataOption('sessionfield'),
	), false, '');
$this->Attributes[] = new weTagData_textAttribute('nameto', false, '');
