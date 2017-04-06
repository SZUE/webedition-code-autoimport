<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
*/
$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$required = new we_tagData_textAttribute('required', true, 'customer');
$loadFields = new we_tagData_textAttribute('loadFields', false, 'customer');
$customerEmailField = new we_tagData_textAttribute('customerEmailField', false, 'customer');
$expireToken = new we_tagData_textAttribute('expireToken', false, 'customer');
$pwdVal = new we_tagData_textAttribute('passwordRule', false, 'customer');

$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('direct', false, 'customer', [$required, $loadFields, $pwdVal], [$required]),
	new we_tagData_option('email', false, 'customer', [$required, $customerEmailField, $expireToken, $loadFields, $pwdVal], [$required, $customerEmailField]),
	new we_tagData_option('emailPassword', false, 'customer', [$required, $customerEmailField, $expireToken, $loadFields, $pwdVal], [$required]),
	new we_tagData_option('resetFromMail', false, 'customer', [$required, $pwdVal], [$required]),
	], true, '');
