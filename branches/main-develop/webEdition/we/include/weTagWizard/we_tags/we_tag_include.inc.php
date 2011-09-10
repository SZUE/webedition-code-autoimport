<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_typeAttribute('type', array(new weTagDataOption('document', false, '', array('id703_type','id314_id','id315_path','id316_gethttp','id317_seeMode','id1861_kind','id632_name','id668_rootdir'), array()), new weTagDataOption('template', false, '', array('id703_type','id315_path','id704_id'), array())), false, '');
$this->Attributes[] = new weTagData_selectAttribute('included', array(), false, '');
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('id',FILE_TABLE, 'text/webedition', false, ''); }
$this->Attributes[] = new weTagData_textAttribute('path', false, '');
$this->Attributes[] = new weTagData_selectAttribute('gethttp', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('seeMode', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('kind', array(new weTagDataOption('all', false, ''), new weTagDataOption('int', false, ''), new weTagDataOption('ext', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('name', false, '');
if(defined("TEMPLATES_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('id',TEMPLATES_TABLE, 'text/weTmpl', false, ''); }
$this->Attributes[] = new weTagData_textAttribute('rootdir', false, '');
