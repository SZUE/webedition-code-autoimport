<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'input_tags';
$this->Module = 'collection';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Attributes = array(
	new weTagData_selectorAttribute('id', VFILE_TABLE, we_base_ContentTypes::COLLECTION, true, ''),
	new weTagData_textAttribute('name', true, ''),
);
