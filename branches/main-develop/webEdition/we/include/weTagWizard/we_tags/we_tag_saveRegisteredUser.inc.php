<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('userexists', false, '', true);
$this->Attributes[] = new weTagData_textAttribute('userempty', false, '', true);
$this->Attributes[] = new weTagData_textAttribute('passempty', false, '', true);
$this->Attributes[] = new weTagData_selectAttribute('register', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_textAttribute('allowed', false, '');
$this->Attributes[] = new weTagData_textAttribute('protected', false, '');
$this->Attributes[] = new weTagData_selectAttribute('changesessiondata', weTagData_selectAttribute::getTrueFalse(), false, '');
