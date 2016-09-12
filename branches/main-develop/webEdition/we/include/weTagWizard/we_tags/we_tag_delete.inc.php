<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$id = new weTagData_textAttribute('id', false, '');
$doctype = new weTagData_sqlRowAttribute('doctype', DOC_TYPES_TABLE, false, 'DocType', 'DocType', 'DocType', '');
$classid = (defined('OBJECT_TABLE') ? new weTagData_selectorAttribute('classid', OBJECT_TABLE, 'object', false, '') : null);
$pid = new weTagData_selectorAttribute('pid', FILE_TABLE, weTagData_selectorAttribute::FOLDER, false, '');
$pidO = (defined('OBJECT_FILES_TABLE') ? new weTagData_selectorAttribute('pid', OBJECT_FILES_TABLE, weTagData_selectorAttribute::FOLDER, false, '') : null);
$protected = new weTagData_selectAttribute('protected', weTagData_selectAttribute::getTrueFalse(), false, '');
$admin = new weTagData_textAttribute('admin', false, '');
$forceedit = new weTagData_selectAttribute('forceedit', weTagData_selectAttribute::getTrueFalse(), false, '');
$mail = new weTagData_textAttribute('mail', false, '');
$mailfrom = new weTagData_textAttribute('mailfrom', false, '');
$charset = new weTagData_textAttribute('charset', false, '');
$userid = new weTagData_textAttribute('userid', false, '');

$this->TypeAttribute = new weTagData_typeAttribute('type', [new weTagDataOption('document', false, '', [$doctype, $pid, $userid, $admin, $forceedit, $mail, $mailfrom, $charset, $protected, $id], []),
	new weTagDataOption('object', false, '', [$classid, $userid, $admin, $forceedit, $mail, $mailfrom, $charset, $pidO, $protected, $id], [])], false, '');

$this->Attributes = [$doctype, $classid, $pid, $pidO, $protected, $admin, $forceedit, $mail, $mailfrom, $charset, $userid, $id];
