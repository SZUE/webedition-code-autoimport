<?php
$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module='voting';

$this->Attributes[] = new weTagData_textAttribute('621', 'id', false, '');
$this->Attributes[] = new weTagData_selectAttribute('844', 'allowredirect', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('866', 'writeto', array(new weTagDataOption('voting', false, ''), new weTagDataOption('session', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('846', 'deletesessiondata', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('845', 'additionalfields', false, '');
