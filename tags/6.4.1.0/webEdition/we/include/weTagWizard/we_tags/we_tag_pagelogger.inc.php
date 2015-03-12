<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('standard'),
	new weTagDataOption('robot'),
	new weTagDataOption('fileserver'),
	new weTagDataOption('downloads'),
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('ssl', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_textAttribute('websitename', false, '');
$this->Attributes[] = new weTagData_selectAttribute('trackname', array(new weTagDataOption('WE_PATH'),
	new weTagDataOption('WE_TITLE'),
	), false, '');
$this->Attributes[] = new weTagData_textAttribute('category', false, '');
$this->Attributes[] = new weTagData_selectAttribute('order', array(new weTagDataOption('FILENAME'),
	new weTagDataOption('FILETITLE'),
	new weTagDataOption('FILESIZE'),
	new weTagDataOption('DOWNLOADS'),
	new weTagDataOption('LASTDOWNLOAD'),
	new weTagDataOption('SHORTDESC'),
	new weTagDataOption('LONGDESC'),
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('desc', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_textAttribute('rows', false, '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');
