<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_typeAttribute('type', array(new weTagDataOption('document', false, '', array('id886_type','id553_id','id734_cachelifetime','id884_hidedirindex','id478_to','id479_nameto'), array('id553_id')), new weTagDataOption('object', false, 'object', array('id886_type','id887_id','id888_triggerid','id734_cachelifetime','id884_hidedirindex','id885_objectseourls','id478_to','id479_nameto'), array('id887_id'))), false, '');
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('id',FILE_TABLE, 'text/webedition,image/*,text/css,text/js,application/*', true, ''); }
if(defined("OBJECT_FILES_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('id',OBJECT_FILES_TABLE, 'objectFile', true, ''); }
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('triggerid',FILE_TABLE, 'text/webedition', false, ''); }

$this->Attributes[] = new weTagData_selectAttribute('hidedirindex', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('objectseourls', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('nameto', false, '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');

