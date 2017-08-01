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

$formname = new we_tagData_textAttribute('formname', false, '');
$publish = new we_tagData_selectAttribute('publish', we_tagData_selectAttribute::getTrueFalse(), false, '');
$searchable = new we_tagData_selectAttribute('searchable', we_tagData_selectAttribute::getTrueFalse(), false, '');

$doctype = new we_tagData_sqlRowAttribute('doctype', DOC_TYPES_TABLE, true, 'DocType', '', '', '');
$tid = (defined('TEMPLATES_TABLE') ? new we_tagData_selectorAttribute('tid', TEMPLATES_TABLE, we_base_ContentTypes::TEMPLATE, false, '') : null);
$categories = new we_tagData_multiSelectorAttribute('categories', CATEGORY_TABLE, '', 'Path', false, '');
$classid = (defined('OBJECT_TABLE') ? new we_tagData_selectorAttribute('classid', OBJECT_TABLE, 'object', false, '') : null);
$protected = new we_tagData_selectAttribute('protected', we_tagData_selectAttribute::getTrueFalse(), false, 'customer');
$admin = new we_tagData_textAttribute('admin', false, '');
$forceedit = new we_tagData_selectAttribute('forceedit', we_tagData_selectAttribute::getTrueFalse(), false, '');
$mail = new we_tagData_textAttribute('mail', false, '');
$mailfrom = new we_tagData_textAttribute('mailfrom', false, '');
$charset = new we_tagData_textAttribute('charset', false, '');
$triggerid = new we_tagData_selectorAttribute('triggerid', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$workspaces = new we_tagData_textAttribute('workspaces', false, '');
$parentid = (defined('OBJECT_FILES_TABLE') ? new we_tagData_selectorAttribute('parentid', OBJECT_FILES_TABLE, we_tagData_selectorAttribute::FOLDER, false, '') : null);
$userid = new we_tagData_textAttribute('userid', false, '');
$name = new we_tagData_textAttribute('name', false, '');
$onduplicate = new we_tagData_selectAttribute('onduplicate', [new we_tagData_option('abort'),
	new we_tagData_option('overwrite'),
	new we_tagData_option('increment'),
	], false, '');
$onpredefinedname = new we_tagData_selectAttribute('onpredefinedname', [new we_tagData_option('appendto'),
	new we_tagData_option('infrontof'),
	new we_tagData_option('overwrite'),
	], false, '');
$workflowname = new we_tagData_textAttribute('workflowname', false, '');
$workflowuserid = new we_tagData_textAttribute('workflowuserid', false, '');

$locales[] = new we_tagData_option('self');
$locales[] = new we_tagData_option('top');
$language = new we_tagData_choiceAttribute('language', $locales, false, true, '');

$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('document', false, '', [$formname, $publish, $searchable, $language, $doctype, $tid, $categories, $userid, $admin, $forceedit, $mail, $mailfrom, $charset, $protected, $workflowname, $workflowuserid], [$doctype]),
	new we_tagData_option('object', false, '', [$formname, $publish, $searchable, $language, $categories, $classid, $name, $onduplicate, $onpredefinedname, $userid, $admin, $forceedit, $mail, $mailfrom, $charset, $triggerid, $parentid, $protected, $workflowname, $workflowuserid], [$classid])], false, '');

$this->Attributes = [$formname, $publish, $searchable, $language, $doctype, $tid, $categories, $classid, $protected, $admin, $forceedit, $mail, $mailfrom, $charset, $triggerid,
	$workspaces, $parentid, $userid, $name, $onduplicate, $onpredefinedname, $workflowname, $workflowuserid];
