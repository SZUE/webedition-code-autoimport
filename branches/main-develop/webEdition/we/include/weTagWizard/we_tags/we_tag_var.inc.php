<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('name', true, '');
$this->Attributes[] = new weTagData_typeAttribute('type', array(new weTagDataOption('document', false, '', array('id582_type','id581_name','id583_doc','id720_htmlspecialchars','id734_cachelifetime'), array('id581_name')), new weTagDataOption('property', false, '', array('id582_type','id581_name','id583_doc','id734_cachelifetime'), array('id581_name')), new weTagDataOption('global', false, '', array('id582_type','id581_name','id720_htmlspecialchars'), array('id581_name')), new weTagDataOption('img', false, '', array('id582_type','id581_name','id583_doc','id720_htmlspecialchars','id734_cachelifetime'), array('id581_name')), new weTagDataOption('href', false, '', array('id582_type','id581_name','id583_doc','id720_htmlspecialchars','id734_cachelifetime'), array('id581_name')), new weTagDataOption('date', false, '', array('id582_type','id581_name','id583_doc','id720_htmlspecialchars','id734_cachelifetime'), array('id581_name')), new weTagDataOption('link', false, '', array('id582_type','id581_name','id583_doc','id720_htmlspecialchars','id734_cachelifetime'), array('id581_name')), new weTagDataOption('multiobject', false, '', array('id582_type'), array()), new weTagDataOption('request', false, '', array('id582_type','id581_name','id720_htmlspecialchars','id734_cachelifetime'), array('id581_name')), new weTagDataOption('post', false, '', array('id582_type','id581_name','id720_htmlspecialchars','id734_cachelifetime'), array('id581_name')), new weTagDataOption('get', false, '', array('id582_type','id581_name','id720_htmlspecialchars','id734_cachelifetime'), array('id581_name')), new weTagDataOption('select', false, '', array('id582_type','id581_name','id583_doc','id720_htmlspecialchars','id734_cachelifetime'), array('id581_name')), new weTagDataOption('session', false, '', array('id582_type','id581_name','id720_htmlspecialchars'), array('id581_name')), new weTagDataOption('shopVat', false, '', array('id582_type'), array())), true, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('win2iso', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('htmlspecialchars', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('nameto', false, '');

//$this->Attributes[] = new weTagData_textAttribute('734', 'cachelifetime', false, '');
