<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_typeAttribute('type', array(new weTagDataOption('text', false, '', array('id319_type','id318_name','id320_size','id321_maxlength','id324_value','id326_html','id328_php','id329_num_format','id330_precision','id637_user','id720_htmlspecialchars','id731_spellcheck','id478_to','id479_nameto','id734_cachelifetime'), array('id318_name')), new weTagDataOption('checkbox', false, '', array('id319_type','id318_name','id324_value','id635_reload','id637_user','id720_htmlspecialchars','id478_to','id479_nameto','id734_cachelifetime'), array('id318_name')), new weTagDataOption('date', false, '', array('id319_type','id318_name','id322_format','id637_user','id720_htmlspecialchars','id478_to','id479_nameto','id734_cachelifetime'), array('id318_name')), new weTagDataOption('choice', false, '', array('id319_type','id318_name','id320_size','id321_maxlength','id323_mode','id325_values','id635_reload','id636_seperator','id637_user','id720_htmlspecialchars','id478_to','id479_nameto','id734_cachelifetime'), array('id318_name')), new weTagDataOption('select', false, '', array('id319_type','id318_name','id325_values','id720_htmlspecialchars','id478_to','id479_nameto','id734_cachelifetime'), array('id318_name')), new weTagDataOption('country', false, '', array('id319_type','id318_name','id872_outputlanguage','id855_doc','id478_to','id479_nameto','id734_cachelifetime'), array('id318_name')), new weTagDataOption('language', false, '', array('id319_type','id318_name','id872_outputlanguage','id855_doc','id478_to','id479_nameto','id734_cachelifetime'), array('id318_name'))), true, '');
$this->Attributes[] = new weTagData_textAttribute('name', true, '');
$this->Attributes[] = new weTagData_textAttribute('size', false, '');
$this->Attributes[] = new weTagData_textAttribute('maxlength', false, '');
$this->Attributes[] = new weTagData_textAttribute('format', false, '');
$this->Attributes[] = new weTagData_selectAttribute('mode', array(new weTagDataOption('add', false, ''), new weTagDataOption('replace', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('value', false, '');
$this->Attributes[] = new weTagData_textAttribute('values', false, '');
$this->Attributes[] = new weTagData_selectAttribute('html', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('htmlspecialchars', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('php', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('num_format', array(new weTagDataOption('german', false, ''), new weTagDataOption('english', false, ''), new weTagDataOption('french', false, ''), new weTagDataOption('swiss', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('precision', false, '');
$this->Attributes[] = new weTagData_selectAttribute('win2iso', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('reload', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('seperator', false, '');
$this->Attributes[] = new weTagData_multiSelectorAttribute('user',USER_TABLE, 'user,folder', 'Text', false, 'users');
$this->Attributes[] = new weTagData_selectAttribute('spellcheck', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'spellchecker');
$this->Attributes[] = new weTagData_textAttribute('outputlanguage', false, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('nameto', false, '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');
