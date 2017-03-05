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

$this->Attributes = array(
	new weTagData_textAttribute('index', false, ''),
	new weTagData_textAttribute('separator', false, ''),
	new weTagData_textAttribute('home', false, ''),
	new weTagData_selectAttribute('hidehome', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_textAttribute('field', false, ''),
	new weTagData_textAttribute('dirfield', false, ''),
	new weTagData_selectAttribute('fieldforfolder', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_selectAttribute('doc', array(
		new weTagDataOption('top'),
		new weTagDataOption('self'),
			), false, ''),
	new weTagData_textAttribute('max', false, ''),
	new weTagData_textAttribute('style'),
	new weTagData_textAttribute('class'),
);
