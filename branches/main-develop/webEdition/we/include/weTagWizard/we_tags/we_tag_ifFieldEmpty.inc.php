<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('match', true, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('img'),
	new weTagDataOption('flashmovie'),
	new weTagDataOption('binary'),
	new weTagDataOption('href'),
	new weTagDataOption('object'),
	new weTagDataOption('multiobject'),
	new weTagDataOption('calendar'),
	new weTagDataOption('checkbox'),
	new weTagDataOption('int'),
	new weTagDataOption('float'),
	new weTagDataOption('collection'),
	), false, '');
