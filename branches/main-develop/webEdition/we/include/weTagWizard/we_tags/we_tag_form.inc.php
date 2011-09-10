<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

if (defined('FORMMAIL_VIAWEDOC') && FORMMAIL_VIAWEDOC ==1){
$this->Attributes[] = new weTagData_typeAttribute('type', array(new weTagDataOption('-', false, '', array('id152_type','id153_id','id154_name','id831_nameid','id155_method','id156_target','id830_enctype'), array()), new weTagDataOption('document', false, '', array('id152_type','id153_id','id154_name','id831_nameid','id155_method','id156_target','id173_doctype','id175_tid','id830_enctype'), array('id173_doctype')), new weTagDataOption('formmail', false, '', array('id152_type','id100_id','id154_name','id831_nameid','id155_method','id156_target','id157_recipient','id158_onsuccess','id159_onerror','id160_onmailerror','id161_onrecipienterror','id162_from','id163_subject','id164_charset','id165_order','id166_required','id167_remove','id168_mimetype','id169_confirmmail','id170_forcefrom','id171_preconfirm','id172_postconfirm'), array('id157_recipient')), new weTagDataOption('object', false, 'object', array('id152_type','id153_id','id154_name','id831_nameid','id155_method','id156_target','id174_categories','id177_classid','id639_parentid','id830_enctype'), array('id177_classid')), new weTagDataOption('search', false, '', array('id152_type','id153_id','id154_name','id831_nameid','id155_method','id156_target'), array()), new weTagDataOption('shopliste', false, '', array('id152_type','id153_id','id831_nameid','id155_method','id156_target'), array())), false, '');
} else {
$this->Attributes[] = new weTagData_typeAttribute('type', array(new weTagDataOption('-', false, '', array('id152_type','id153_id','id154_name','id831_nameid','id155_method','id156_target','id830_enctype'), array()), new weTagDataOption('document', false, '', array('id152_type','id153_id','id154_name','id831_nameid','id155_method','id156_target','id173_doctype','id175_tid','id830_enctype'), array('id173_doctype')), new weTagDataOption('formmail', false, '', array('id152_type','id154_name','id831_nameid','id155_method','id156_target','id157_recipient','id158_onsuccess','id159_onerror','id160_onmailerror','id161_onrecipienterror','id162_from','id163_subject','id164_charset','id165_order','id166_required','id167_remove','id168_mimetype','id169_confirmmail','id170_forcefrom','id171_preconfirm','id172_postconfirm'), array('id157_recipient')), new weTagDataOption('object', false, 'object', array('id152_type','id153_id','id154_name','id831_nameid','id155_method','id156_target','id174_categories','id177_classid','id639_parentid','id830_enctype'), array('id177_classid')), new weTagDataOption('search', false, '', array('id152_type','id153_id','id154_name','id831_nameid','id155_method','id156_target'), array()), new weTagDataOption('shopliste', false, '', array('id152_type','id153_id','id831_nameid','id155_method','id156_target'), array())), false, '');
}
$this->Attributes[] = new weTagData_selectorAttribute('id',FILE_TABLE, 'text/webedition', true, '');
$this->Attributes[] = new weTagData_textAttribute('enctype', false, '');
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('id',FILE_TABLE, 'text/webedition', false, ''); }
$this->Attributes[] = new weTagData_textAttribute('name', false, '');
$this->Attributes[] = new weTagData_textAttribute('nameid', false, '');
$this->Attributes[] = new weTagData_selectAttribute('method', array(new weTagDataOption('get', false, ''), new weTagDataOption('post', false, '')), false, '');
$this->Attributes[] = new weTagData_choiceAttribute('target', array(new weTagDataOption('_top', false, ''), new weTagDataOption('_parent', false, ''), new weTagDataOption('_self', false, ''), new weTagDataOption('_blank', false, '')), false,false, '');
$this->Attributes[] = new weTagData_textAttribute('recipient', true, '');
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('onsuccess',FILE_TABLE, 'text/webedition', false, ''); }
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('onerror',FILE_TABLE, 'text/webedition', false, ''); }
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('onmailerror',FILE_TABLE, 'text/webedition', false, ''); }
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('onrecipienterror',FILE_TABLE, 'text/webedition', false, ''); }
$this->Attributes[] = new weTagData_textAttribute('from', false, '');
$this->Attributes[] = new weTagData_textAttribute('subject', false, '');
$this->Attributes[] = new weTagData_textAttribute('charset', false, '');
$this->Attributes[] = new weTagData_textAttribute('order', false, '');
$this->Attributes[] = new weTagData_textAttribute('required', false, '');
$this->Attributes[] = new weTagData_textAttribute('remove', false, '');
$this->Attributes[] = new weTagData_selectAttribute('mimetype', array(new weTagDataOption('text/plain', false, ''), new weTagDataOption('text/html', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('confirmmail', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('forcefrom', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('preconfirm', false, '');
$this->Attributes[] = new weTagData_textAttribute('postconfirm', false, '');
$this->Attributes[] = new weTagData_sqlRowAttribute('doctype',DOC_TYPES_TABLE, true, 'DocType', '', '', '');
$this->Attributes[] = new weTagData_multiSelectorAttribute('categories',CATEGORY_TABLE, '', 'Path', false, '');
if(defined("TEMPLATES_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('tid',TEMPLATES_TABLE, 'text/weTmpl', false, ''); }
if(defined("OBJECT_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('classid',OBJECT_TABLE, 'object', false, 'object'); }
if(defined("OBJECT_FILES_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('parentid',OBJECT_FILES_TABLE, 'folder', false, ''); }
$this->Attributes[] = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('enctype', array(new weTagDataOption('application/x-www-form-urlencoded', false, ''), new weTagDataOption('multipart/form-data', false, '')), false, '');
