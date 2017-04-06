<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
*/
$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new we_tagData_choiceAttribute('pages', [new we_tagData_option('all'),
	new we_tagData_option('properties'),
	new we_tagData_option('edit'),
	new we_tagData_option('information'),
	new we_tagData_option('preview'),
	new we_tagData_option('validation'),
	new we_tagData_option('customer'),
	new we_tagData_option('versions'),
	new we_tagData_option('schedpro'),
	new we_tagData_option('variants'),
 ], false, true, '');
