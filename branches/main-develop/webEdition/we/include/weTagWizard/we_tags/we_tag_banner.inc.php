<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
*/

$this->Module = 'banner';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);


$this->Attributes[] = new weTagData_textAttribute('name', true, '');
$this->Attributes[] = new weTagData_textAttribute('width', false, '');
$this->Attributes[] = new weTagData_textAttribute('height', false, '');
$this->Attributes[] = new weTagData_textAttribute('paths', false, '');
$this->Attributes[] = new weTagData_selectAttribute('type', [new weTagDataOption('js'),
	new weTagDataOption('iframe'),
	new weTagDataOption('cookie'),
	new weTagDataOption('pixel'),
	], false, '');
$this->Attributes[] = new weTagData_choiceAttribute('target', [new weTagDataOption('_top'),
	new weTagDataOption('_parent'),
	new weTagDataOption('_self'),
	new weTagDataOption('_blank'),
	], false, false, '');
$this->Attributes[] = new weTagData_selectAttribute('link', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_textAttribute('clickscript', false, '');
$this->Attributes[] = new weTagData_textAttribute('getscript', false, '');
$this->Attributes[] = new weTagData_textAttribute('page', false, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, '');
