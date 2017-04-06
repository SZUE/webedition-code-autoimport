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
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Attributes[] = new we_tagData_selectAttribute('name', [
	new we_tagData_option('banner'),
	new we_tagData_option('customer'),
	new we_tagData_option('glossary'),
	new we_tagData_option('newsletter'),
	new we_tagData_option('object'),
	new we_tagData_option('shop'),
	new we_tagData_option('scheduler'),
	new we_tagData_option('voting'),
	new we_tagData_option('workflow'),
	], true);
