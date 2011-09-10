<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'if_tags';
$this->Module = 'users';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_selectAttribute('27', 'type', array(new weTagDataOption('name', false, ''), new weTagDataOption('initials', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('28', 'doc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('29', 'creator', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('734', 'cachelifetime', false, '');
