<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('id', false, '');
$this->Attributes[] = new weTagData_textAttribute('subject', false, '');
$this->Attributes[] = new weTagData_textAttribute('recipient', true, '');
$this->Attributes[] = new weTagData_textAttribute('recipientCC', false, '');
$this->Attributes[] = new weTagData_textAttribute('recipientBCC', false, '');
$this->Attributes[] = new weTagData_textAttribute('from', true, '');
$this->Attributes[] = new weTagData_textAttribute('reply', false, '');
$this->Attributes[] = new weTagData_selectAttribute('mimetype', array(new weTagDataOption('text/plain', false, ''), new weTagDataOption('text/html', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('charset', false, '');
$this->Attributes[] = new weTagData_selectAttribute('includeimages', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('usebasehref', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('useformmaillog', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('useformmailblock', weTagData_selectAttribute::getTrueFalse(), false, '');
