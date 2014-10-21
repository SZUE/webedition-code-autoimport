<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_choiceAttribute('type', array(new weTagDataOption('xml'),
	new weTagDataOption('html'),
	new weTagDataOption('js'),
	new weTagDataOption('php'),
	), false, false, '');
