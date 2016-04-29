<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);


$error = new weTagData_selectAttribute('onerror', array(
	new weTagDataOption('all'),
	new weTagDataOption('nousername'),
	new weTagDataOption('nopassword'),
	new weTagDataOption('userexists'),
	new weTagDataOption('passwordRule'),
	), false, '');

$this->TypeAttribute = new weTagData_typeAttribute('type', array(
	new weTagDataOption('document', false, '', array(), array()),
	new weTagDataOption('object', false, 'object', array(), array()),
	new weTagDataOption('customer', false, 'customer', array($error), array($error)))
	, false, '');


$this->Attributes = array(
	$error,
	new weTagData_textAttribute('formname', false, '')
);
