<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$formname = new weTagData_textAttribute('formname', false, '');
$publish = new weTagData_selectAttribute('publish', weTagData_selectAttribute::getTrueFalse(), false, '');
$searchable = new weTagData_selectAttribute('searchable', weTagData_selectAttribute::getTrueFalse(), false, '');

$doctype = new weTagData_sqlRowAttribute('doctype',DOC_TYPES_TABLE, true, 'DocType', '', '', '');
$tid = (defined("TEMPLATES_TABLE") ? new weTagData_selectorAttribute('tid',TEMPLATES_TABLE, 'text/weTmpl', false, ''): null);
$categories = new weTagData_multiSelectorAttribute('categories',CATEGORY_TABLE, '', 'Path', false, '');
$classid = (defined("OBJECT_TABLE") ? new weTagData_selectorAttribute('classid',OBJECT_TABLE, 'object', false, ''):null);
$protected = new weTagData_selectAttribute('protected', weTagData_selectAttribute::getTrueFalse(), false, 'customer');
$admin = new weTagData_textAttribute( 'admin', false, '');
$forceedit = new weTagData_selectAttribute('forceedit', weTagData_selectAttribute::getTrueFalse(), false, '');
$mail = new weTagData_textAttribute('mail', false, '');
$mailfrom = new weTagData_textAttribute('mailfrom', false, '');
$charset = new weTagData_textAttribute('charset', false, '');
$triggerid = new weTagData_selectorAttribute('triggerid',FILE_TABLE, 'text/webedition', false, '');
$workspaces = new weTagData_textAttribute('workspaces', false, '');
$parentid = (defined("OBJECT_FILES_TABLE") ? new weTagData_selectorAttribute('parentid',OBJECT_FILES_TABLE, weTagData_selectorAttribute::FOLDER, false, ''):null);
$userid = new weTagData_textAttribute('userid', false, '');
$name = new weTagData_textAttribute('name', false, '');
$onduplicate = new weTagData_selectAttribute('onduplicate', array(new weTagDataOption('abort', false, ''), new weTagDataOption('overwrite', false, ''), new weTagDataOption('increment', false, '')), false, '');
$onpredefinedname = new weTagData_selectAttribute('onpredefinedname', array(new weTagDataOption('appendto', false, ''), new weTagDataOption('infrontof', false, ''), new weTagDataOption('overwrite', false, '')), false, '');
$workflowname = new weTagData_textAttribute('workflowname', false, '');
$workflowuserid = new weTagData_textAttribute('workflowuserid', false, '');

$this->TypeAttribute = new weTagData_typeAttribute('type', array(
	new weTagDataOption('document', false, '', array($formname,$publish,$searchable,$doctype,$tid,$categories,$userid,$admin,$forceedit,$mail,$mailfrom,$charset,$protected,$workflowname,$workflowuserid), array($doctype)),
	new weTagDataOption('object', false, '', array($formname,$publish,$searchable,$categories,$classid,$name,$onduplicate,$onpredefinedname,$userid,$admin,$forceedit,$mail,$mailfrom,$charset,$triggerid,$parentid,$protected,$workflowname,$workflowuserid), array($classid))), false, '');

$this->Attributes=array($formname,$publish,$searchable,$doctype,$tid,$categories,$classid,$protected,$admin,$forceedit,$mail,$mailfrom,$charset,$triggerid,
	$workspaces,$parentid,$userid,$name,$onduplicate,$onpredefinedname,$workflowname,$workflowuserid);
