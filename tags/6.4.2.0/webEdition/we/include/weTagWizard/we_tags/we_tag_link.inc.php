<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('name', false, '');
$this->Attributes[] = new weTagData_selectAttribute('only', array(new weTagDataOption('href'),
	new weTagDataOption('jsstatus'),
	new weTagDataOption('jsscrollbars'),
	new weTagDataOption('jsmenubar'),
	new weTagDataOption('jstoolbar'),
	new weTagDataOption('jsresizable'),
	new weTagDataOption('jslocation'),
	new weTagDataOption('img_id'),
	new weTagDataOption('type'),
	new weTagDataOption('ctype'),
	new weTagDataOption('border'),
	new weTagDataOption('hspace'),
	new weTagDataOption('vspace'),
	new weTagDataOption('align'),
	new weTagDataOption('alt'),
	new weTagDataOption('jsheight'),
	new weTagDataOption('jswidth'),
	new weTagDataOption('jsposx'),
	new weTagDataOption('id'),
	new weTagDataOption('text'),
	new weTagDataOption('title'),
	new weTagDataOption('accesskey'),
	new weTagDataOption('tabindex'),
	new weTagDataOption('lang'),
	new weTagDataOption('rel'),
	new weTagDataOption('obj_id'),
	new weTagDataOption('anchor'),
	new weTagDataOption('params'),
	new weTagDataOption('target'),
	new weTagDataOption('jswin'),
	new weTagDataOption('jscenter'),
	new weTagDataOption('jsposy'),
	new weTagDataOption('img_title'),
	), false, '');
$this->Attributes[] = new weTagData_textAttribute('class', false, '');
$this->Attributes[] = new weTagData_textAttribute('style', false, '');
$this->Attributes[] = new weTagData_textAttribute('text', false, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, '');
if(defined('FILE_TABLE')){
	$this->Attributes[] = new weTagData_selectorAttribute('id', FILE_TABLE, 'text/webedition', false, '');
}
if(defined('FILE_TABLE')){
	$this->Attributes[] = new weTagData_selectorAttribute('imageid', FILE_TABLE, 'image/*', false, '');
}
$this->Attributes[] = new weTagData_selectAttribute('hidedirindex', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('objectseourls', weTagData_selectAttribute::getTrueFalse(), false, '');
