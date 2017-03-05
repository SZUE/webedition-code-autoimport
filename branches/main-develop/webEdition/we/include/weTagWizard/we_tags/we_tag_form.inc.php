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

$id = new weTagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$name = new weTagData_textAttribute('name', false, '');
$nameid = new weTagData_textAttribute('nameid', false, '');
$method = new weTagData_selectAttribute('method', [new weTagDataOption('get'),
	new weTagDataOption('post'),
	], false, '');
$target = new weTagData_choiceAttribute('target', [new weTagDataOption('_top'),
	new weTagDataOption('_parent'),
	new weTagDataOption('_self'),
	new weTagDataOption('_blank'),
	], false, false, '');
$recipient = new weTagData_textAttribute('recipient', true, '');
$onsuccess = new weTagData_selectorAttribute('onsuccess', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$onerror = new weTagData_selectorAttribute('onerror', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$onmailerror = new weTagData_selectorAttribute('onmailerror', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$onrecipienterror = new weTagData_selectorAttribute('onrecipienterror', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$from = new weTagData_textAttribute('from', false, '');
$subject = new weTagData_textAttribute('subject', false, '');
$charset = new weTagData_textAttribute('charset', false, '');
$order = new weTagData_textAttribute('order', false, '');
$required = new weTagData_textAttribute('required', false, '');
$remove = new weTagData_textAttribute('remove', false, '');
$mimetype = new weTagData_selectAttribute('mimetype', [new weTagDataOption('text/plain'),
	new weTagDataOption('text/html'),
	], false, '');
$confirmmail = new weTagData_selectAttribute('confirmmail', weTagData_selectAttribute::getTrueFalse(), false, '');
$forcefrom = new weTagData_selectAttribute('forcefrom', weTagData_selectAttribute::getTrueFalse(), false, '');
$preconfirm = new weTagData_textAttribute('preconfirm', false, '');
$postconfirm = new weTagData_textAttribute('postconfirm', false, '');
$doctype = new weTagData_sqlRowAttribute('doctype', DOC_TYPES_TABLE, true, 'DocType', '', '', '');
$categories = new weTagData_multiSelectorAttribute('categories', CATEGORY_TABLE, '', 'Path', false, '');
$tid = (defined('TEMPLATES_TABLE') ? new weTagData_selectorAttribute('tid', TEMPLATES_TABLE, 'text/weTmpl', false, '') : null);
$classid = (defined('OBJECT_TABLE') ? new weTagData_selectorAttribute('classid', OBJECT_TABLE, 'object', false, 'object') : null);
$parentid = (defined('OBJECT_FILES_TABLE') ? new weTagData_selectorAttribute('parentid', OBJECT_FILES_TABLE, weTagData_selectorAttribute::FOLDER, false, '') : null);
$xml = new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, '');
$enctype = new weTagData_selectAttribute('enctype', [new weTagDataOption('application/x-www-form-urlencoded'),
	new weTagDataOption('multipart/form-data'),
	], false, '');

if(defined('FORMMAIL_VIAWEDOC') && FORMMAIL_VIAWEDOC == 1){
	$this->TypeAttribute = new weTagData_typeAttribute('type', [new weTagDataOption('-', false, '', [$id, $name, $nameid, $method, $target, $enctype], []),
		new weTagDataOption('document', false, '', [$id, $name, $nameid, $method, $target, $doctype, $tid, $enctype], [$doctype]),
		new weTagDataOption('formmail', false, '', [$id, $name, $nameid, $method, $target, $recipient, $onsuccess, $onerror, $onmailerror, $onrecipienterror, $from, $subject, $charset, $order, $required, $remove, $mimetype, $confirmmail, $forcefrom, $preconfirm, $postconfirm], [$recipient]),
		new weTagDataOption('object', false, 'object', [$id, $name, $nameid, $method, $target, $categories, $classid, $parentid, $enctype], [$classid]),
		new weTagDataOption('search', false, '', [$id, $name, $nameid, $method, $target], []),
		new weTagDataOption('shopliste', false, '', [$id, $nameid, $method, $target], [])
		], false, '');
} else {
	$this->TypeAttribute = new weTagData_typeAttribute('type', [new weTagDataOption('-', false, '', [$id, $name, $nameid, $method, $target, $enctype], []),
		new weTagDataOption('document', false, '', [$id, $name, $nameid, $method, $target, $doctype, $tid, $enctype], [$doctype]),
		new weTagDataOption('formmail', false, '', [$name, $nameid, $method, $target, $recipient, $onsuccess, $onerror, $onmailerror, $onrecipienterror, $from, $subject, $charset, $order, $required, $remove, $mimetype, $confirmmail, $forcefrom, $preconfirm, $postconfirm], [$recipient]),
		new weTagDataOption('object', false, 'object', [$id, $name, $nameid, $method, $target, $categories, $classid, $parentid, $enctype], [$classid]),
		new weTagDataOption('search', false, '', [$id, $name, $nameid, $method, $target], []),
		new weTagDataOption('shopliste', false, '', [$id, $nameid, $method, $target], [])
		], false, '');
}


$this->Attributes = [$id, $name, $nameid, $method, $target, $recipient, $onsuccess, $onerror, $onmailerror, $from, $subject, $charset, $order, $required, $remove, $mimetype,
	$confirmmail, $forcefrom, $preconfirm, $postconfirm, $doctype, $categories, $tid, $classid, $parentid, $xml, $enctype];
