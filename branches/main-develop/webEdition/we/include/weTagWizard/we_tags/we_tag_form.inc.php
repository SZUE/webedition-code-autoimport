<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 */
$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$id = new we_tagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$name = new we_tagData_textAttribute('name', false, '');
$nameid = new we_tagData_textAttribute('nameid', false, '');
$method = new we_tagData_selectAttribute('method', [new we_tagData_option('get'),
	new we_tagData_option('post'),
	], false, '');
$target = new we_tagData_choiceAttribute('target', [new we_tagData_option('_top'),
	new we_tagData_option('_parent'),
	new we_tagData_option('_self'),
	new we_tagData_option('_blank'),
	], false, false, '');
$recipient = new we_tagData_textAttribute('recipient', true, '');
$onsuccess = new we_tagData_selectorAttribute('onsuccess', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$onerror = new we_tagData_selectorAttribute('onerror', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$onmailerror = new we_tagData_selectorAttribute('onmailerror', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$onrecipienterror = new we_tagData_selectorAttribute('onrecipienterror', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$from = new we_tagData_textAttribute('from', false, '');
$subject = new we_tagData_textAttribute('subject', false, '');
$charset = new we_tagData_textAttribute('charset', false, '');
$order = new we_tagData_textAttribute('order', false, '');
$required = new we_tagData_textAttribute('required', false, '');
$remove = new we_tagData_textAttribute('remove', false, '');
$mimetype = new we_tagData_selectAttribute('mimetype', [
	new we_tagData_option('text/plain'),
	new we_tagData_option('text/html'),
	], false, '');
$confirmmail = new we_tagData_selectAttribute('confirmmail', we_tagData_selectAttribute::getTrueFalse(), false, '');
$forcefrom = new we_tagData_selectAttribute('forcefrom', we_tagData_selectAttribute::getTrueFalse(), false, '');
$preconfirm = new we_tagData_textAttribute('preconfirm', false, '');
$postconfirm = new we_tagData_textAttribute('postconfirm', false, '');
$doctype = new we_tagData_sqlRowAttribute('doctype', DOC_TYPES_TABLE, true, 'DocType', '', '', '');
$categories = new we_tagData_multiSelectorAttribute('categories', CATEGORY_TABLE, '', 'Path', false, '');
$tid = (defined('TEMPLATES_TABLE') ? new we_tagData_selectorAttribute('tid', TEMPLATES_TABLE, we_base_ContentTypes::TEMPLATE, false, '') : null);
$classid = (defined('OBJECT_TABLE') ? new we_tagData_selectorAttribute('classid', OBJECT_TABLE, we_base_ContentTypes::OBJECT, false, 'object') : null);
$parentid = (defined('OBJECT_FILES_TABLE') ? new we_tagData_selectorAttribute('parentid', OBJECT_FILES_TABLE, we_tagData_selectorAttribute::FOLDER, false, '') : null);
$xml = new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, '');
$enctype = new we_tagData_selectAttribute('enctype', [new we_tagData_option('application/x-www-form-urlencoded'),
	new we_tagData_option('multipart/form-data'),
	], false, '');

if(defined('FORMMAIL_VIAWEDOC') && FORMMAIL_VIAWEDOC == 1){
	$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('-', false, '', [$id, $name, $nameid, $method, $target, $enctype], []),
		new we_tagData_option('document', false, '', [$id, $name, $nameid, $method, $target, $doctype, $tid, $enctype], [$doctype]),
		new we_tagData_option('formmail', false, '', [$id, $name, $nameid, $method, $target, $recipient, $onsuccess, $onerror, $onmailerror, $onrecipienterror, $from,
			$subject, $charset, $order, $required, $remove, $mimetype, $confirmmail, $forcefrom, $preconfirm, $postconfirm], [$recipient]),
		new we_tagData_option('object', false, 'object', [$id, $name, $nameid, $method, $target, $categories, $classid, $parentid, $enctype], [$classid]),
		new we_tagData_option('search', false, '', [$id, $name, $nameid, $method, $target], []),
		new we_tagData_option('shopliste', false, '', [$id, $nameid, $method, $target], [])
		], false, '');
} else {
	$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('-', false, '', [$id, $name, $nameid, $method, $target, $enctype], []),
		new we_tagData_option('document', false, '', [$id, $name, $nameid, $method, $target, $doctype, $tid, $enctype], [$doctype]),
		new we_tagData_option('formmail', false, '', [$name, $nameid, $method, $target, $recipient, $onsuccess, $onerror, $onmailerror, $onrecipienterror, $from, $subject,
			$charset, $order, $required, $remove, $mimetype, $confirmmail, $forcefrom, $preconfirm, $postconfirm], [$recipient]),
		new we_tagData_option('object', false, 'object', [$id, $name, $nameid, $method, $target, $categories, $classid, $parentid, $enctype], [$classid]),
		new we_tagData_option('search', false, '', [$id, $name, $nameid, $method, $target], []),
		new we_tagData_option('shopliste', false, '', [$id, $nameid, $method, $target], [])
		], false, '');
}


$this->Attributes = [$id, $name, $nameid, $method, $target, $recipient, $onsuccess, $onerror, $onmailerror, $from, $subject, $charset, $order, $required, $remove,
	$mimetype,
	$confirmmail, $forcefrom, $preconfirm, $postconfirm, $doctype, $categories, $tid, $classid, $parentid, $xml, $enctype];
