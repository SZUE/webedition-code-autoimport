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
$this->Attributes[] = new weTagData_selectAttribute('name', [
	new weTagDataOption('banner'),
	new weTagDataOption('customer'),
	new weTagDataOption('glossary'),
	new weTagDataOption('messaging'),
	new weTagDataOption('newsletter'),
	new weTagDataOption('object'),
	new weTagDataOption('shop'),
	new weTagDataOption('scheduler'),
	new weTagDataOption('voting'),
	new weTagDataOption('workflow'),
	], true);
