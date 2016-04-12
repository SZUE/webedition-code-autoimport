<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Attributes[] = new weTagData_selectAttribute('doc', array(
	new weTagDataOption('self'),
	new weTagDataOption('top'),
	new weTagDataOption('listview'),
	), false, '');
