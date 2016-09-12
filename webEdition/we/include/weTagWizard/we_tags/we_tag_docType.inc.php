<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_selectAttribute('doc', [new weTagDataOption('top'),
	new weTagDataOption('self'),
	new weTagDataOption('top'),
	], false, '');
