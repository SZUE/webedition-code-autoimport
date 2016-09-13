<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

if(defined('FILE_TABLE')){
	$this->Attributes[] = new weTagData_selectorAttribute('id', FILE_TABLE, 'text/css', true, '');
}
$this->Attributes[] = new weTagData_selectAttribute('rel', [new weTagDataOption('stylesheet'),
	new weTagDataOption('alternate stylesheet'),
	], false, '');
$this->Attributes[] = new weTagData_textAttribute('title', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('media', [new weTagDataOption('all'),
	new weTagDataOption('braille'),
	new weTagDataOption('embossed'),
	new weTagDataOption('handheld'),
	new weTagDataOption('print'),
	new weTagDataOption('projection'),
	new weTagDataOption('screen'),
	new weTagDataOption('speech'),
	new weTagDataOption('tty'),
	new weTagDataOption('tv'),
	], false, false, '');
$this->Attributes[] = new weTagData_selectAttribute('applyto', [new weTagDataOption('all'),
	new weTagDataOption('wysiwyg'),
	new weTagDataOption('around'),
	], false, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, '');
