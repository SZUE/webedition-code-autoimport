<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$required = new weTagData_textAttribute('required', true, 'customer');
$loadFields = new weTagData_textAttribute('loadFields', false, 'customer');
$customerEmailField = new weTagData_textAttribute('customerEmailField', false, 'customer');
$expireToken = new weTagData_textAttribute('expireToken', false, 'customer');
$pwdVal = new weTagData_textAttribute('passwordRule', false, 'customer');

$this->TypeAttribute = new weTagData_typeAttribute('type', [new weTagDataOption('direct', false, 'customer', [$required, $loadFields, $pwdVal], [$required]),
	new weTagDataOption('email', false, 'customer', [$required, $customerEmailField, $expireToken, $loadFields, $pwdVal], [$required, $customerEmailField]),
	new weTagDataOption('emailPassword', false, 'customer', [$required, $customerEmailField, $expireToken, $loadFields, $pwdVal], [$required]),
	new weTagDataOption('resetFromMail', false, 'customer', [$required, $pwdVal], [$required]),
	], true, '');
