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
$this->Module = 'object';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('field', true, '');
$this->Attributes[] = new weTagData_textAttribute('value', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('compare', array(new weTagDataOption('='),
	new weTagDataOption('!='),
	new weTagDataOption('&lt;'),
	new weTagDataOption('&gt;'),
	new weTagDataOption('&lt;='),
	new weTagDataOption('&gt;='),
	new weTagDataOption('LIKE'),
	), false, false, '');
$this->Attributes[] = new weTagData_textAttribute('var', false, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('global'),
	new weTagDataOption('request'),
	new weTagDataOption('sessionfield'),
	new weTagDataOption('document'),
	new weTagDataOption('now'),
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('property', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('self'),
	new weTagDataOption('top'),
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('exactmatch', array(new weTagDataOption('false'),
	new weTagDataOption('true'),
	), false, '');
