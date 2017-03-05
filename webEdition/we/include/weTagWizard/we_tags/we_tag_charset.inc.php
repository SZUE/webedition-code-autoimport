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

$this->Attributes[] = new weTagData_choiceAttribute('defined', array(new weTagDataOption('UTF-8'),
	new weTagDataOption('ISO-8859-1'),
	new weTagDataOption('ISO-8859-2'),
	new weTagDataOption('ISO-8859-3'),
	new weTagDataOption('ISO-8859-4'),
	new weTagDataOption('ISO-8859-5'),
	new weTagDataOption('ISO-8859-6'),
	new weTagDataOption('ISO-8859-7'),
	new weTagDataOption('ISO-8859-8'),
	new weTagDataOption('ISO-8859-9'),
	new weTagDataOption('ISO-8859-10'),
	new weTagDataOption('ISO-8859-11'),
	new weTagDataOption('ISO-8859-13'),
	new weTagDataOption('ISO-8859-14'),
	new weTagDataOption('ISO-8859-15'),
	new weTagDataOption('Windows-1251'),
	new weTagDataOption('Windows-1252'),
	), false, true, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, '');
