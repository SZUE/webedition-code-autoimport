<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes[] = new weTagData_textAttribute('name', false, '');
$this->Attributes[] = new weTagData_textAttribute('id', false, '');

$this->Attributes[] = new weTagData_textAttribute('condition', false, '');
