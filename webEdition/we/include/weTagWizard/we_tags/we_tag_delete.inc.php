<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
*/
$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$id = new we_tagData_textAttribute('id', false, '');
$doctype = new we_tagData_sqlRowAttribute('doctype', DOC_TYPES_TABLE, false, 'DocType', 'DocType', 'DocType', '');
$classid = (defined('OBJECT_TABLE') ? new we_tagData_selectorAttribute('classid', OBJECT_TABLE, 'object', false, '') : null);
$pid = new we_tagData_selectorAttribute('pid', FILE_TABLE, we_tagData_selectorAttribute::FOLDER, false, '');
$pidO = (defined('OBJECT_FILES_TABLE') ? new we_tagData_selectorAttribute('pid', OBJECT_FILES_TABLE, we_tagData_selectorAttribute::FOLDER, false, '') : null);
$protected = new we_tagData_selectAttribute('protected', we_tagData_selectAttribute::getTrueFalse(), false, '');
$admin = new we_tagData_textAttribute('admin', false, '');
$forceedit = new we_tagData_selectAttribute('forceedit', we_tagData_selectAttribute::getTrueFalse(), false, '');
$mail = new we_tagData_textAttribute('mail', false, '');
$mailfrom = new we_tagData_textAttribute('mailfrom', false, '');
$charset = new we_tagData_textAttribute('charset', false, '');
$userid = new we_tagData_textAttribute('userid', false, '');

$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('document', false, '', [$doctype, $pid, $userid, $admin, $forceedit, $mail, $mailfrom, $charset, $protected, $id], []),
	new we_tagData_option('object', false, '', [$classid, $userid, $admin, $forceedit, $mail, $mailfrom, $charset, $pidO, $protected, $id], [])], false, '');

$this->Attributes = [$doctype, $classid, $pid, $pidO, $protected, $admin, $forceedit, $mail, $mailfrom, $charset, $userid, $id];
