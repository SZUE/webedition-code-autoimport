<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes[] = new weTagData_textAttribute('percent', true, '');
$this->Attributes[] = new weTagData_choiceAttribute('num_format', array(new weTagDataOption('german', false, ''), new weTagDataOption('french', false, ''), new weTagDataOption('english', false, ''), new weTagDataOption('swiss', false, '')), false,false, '');
