<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_typeAttribute('type', array(new weTagDataOption('document', false, '', array('id103_type','id104_doctype','id106_pid','id107_userid','id108_admin','id109_forceedit','id110_mail','id111_mailfrom','id112_charset','id757_protected'), array()), new weTagDataOption('object', false, '', array('id103_type','id105_classid','id107_userid','id108_admin','id109_forceedit','id110_mail','id111_mailfrom','id112_charset','id652_pid','id757_protected'), array())), false, '');
$this->Attributes[] = new weTagData_sqlRowAttribute('doctype',DOC_TYPES_TABLE, false, 'DocType', 'DocType', 'DocType', '');
if(defined("OBJECT_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('classid',OBJECT_TABLE, 'object', false, ''); }
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('pid',FILE_TABLE, 'folder', false, ''); }
if(defined("OBJECT_FILES_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('pid',OBJECT_FILES_TABLE, 'folder', false, ''); }
$this->Attributes[] = new weTagData_selectAttribute('protected', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('admin', false, '');
$this->Attributes[] = new weTagData_selectAttribute('forceedit', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('mail', false, '');
$this->Attributes[] = new weTagData_textAttribute('mailfrom', false, '');
$this->Attributes[] = new weTagData_textAttribute('charset', false, '');
$this->Attributes[] = new weTagData_textAttribute('userid', false, '');
