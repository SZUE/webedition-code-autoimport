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
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('match', true, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('self'),
	new weTagDataOption('top'),
	new weTagDataOption('document'),
	new weTagDataOption('object'),
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('request'),
	new weTagDataOption('post'),
	new weTagDataOption('get'),
	new weTagDataOption('global'),
	new weTagDataOption('session'),
	new weTagDataOption('sessionfield'),
	new weTagDataOption('href'),
	new weTagDataOption('img'),
	new weTagDataOption('multiobject', false, 'object')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('property', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_textAttribute('formname', false, '');
