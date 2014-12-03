<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = (defined('CATEGORY_TABLE') ? new weTagData_selectorAttribute('id', CATEGORY_TABLE, '', false, '') : null);
$this->Attributes[] = new weTagData_selectAttribute('fromdoc', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(
	new weTagDataOption('self'),
	new weTagDataOption('top'),
	new weTagDataOption('listview'),
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('showpath', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_textAttribute('rootdir', false, '');
$this->Attributes[] = new weTagData_selectAttribute('show', array(
	new weTagDataOption('category'),
	new weTagDataOption('vat'),
	//new weTagDataOption('both')
	), false, '');
//$this->Attributes[] = new weTagData_selectAttribute('getobject', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('catfield', array(
	new weTagDataOption('ID'),
	new weTagDataOption('Category'),
	new weTagDataOption('Path'),
	new weTagDataOption('Title'),
	new weTagDataOption('Description'),
	new weTagDataOption('DestPrinciple')
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('vatfield', array(
	new weTagDataOption('id'),
	new weTagDataOption('vat'),
	new weTagDataOption('text'),
	new weTagDataOption('standard'),
	new weTagDataOption('territory'),
	new weTagDataOption('categories')
	), false, '');
