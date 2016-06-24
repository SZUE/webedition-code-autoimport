<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->DefaultValue = '<we:repeat>
<we:field name="Title" alt="we_path" hyperlink="true"/>
</we:repeat>';

$MultiSelector = new weTagData_multiSelectorAttribute('MultiSelector', FILE_TABLE, '', '', false, '');
$name = new weTagData_textAttribute('name', false, '');
$doctype = new weTagData_sqlRowAttribute('doctype', DOC_TYPES_TABLE, false, 'DocType', '', '', '');
$categories = new weTagData_multiSelectorAttribute('categories', CATEGORY_TABLE, '', 'Path', false, '');
$catOr = new weTagData_selectAttribute('catOr', array(new weTagDataOption('true'),
	), false, '');
$rows = new weTagData_textAttribute('rows', false, '');
$cols = new weTagData_textAttribute('cols', false, '');
$order_document = new weTagData_choiceAttribute('order', array(
	new weTagDataOption('random()'),
	new weTagDataOption('we_id'),
	new weTagDataOption('we_filename'),
	new weTagDataOption('we_creationdate'),
	new weTagDataOption('we_moddate'),
	new weTagDataOption('we_published'),
	), false, false, '');
$order_search = new weTagData_choiceAttribute('order', array(new weTagDataOption('random()'),
	new weTagDataOption('we_id'),
	new weTagDataOption('we_filename'),
	new weTagDataOption('we_creationdate'),
	new weTagDataOption('we_moddate'),
	new weTagDataOption('Title'),
	new weTagDataOption('Description'),
	new weTagDataOption('Path'),
	new weTagDataOption('Text'),
	new weTagDataOption('DID'),
	new weTagDataOption('OID'),
	new weTagDataOption('ID'),
	), false, false, '');
$order_category = new weTagData_choiceAttribute('order', array(new weTagDataOption('random()'),
	new weTagDataOption('ID'),
	new weTagDataOption('Category'),
	new weTagDataOption('Text'),
	new weTagDataOption('Path'),
	), false, false, '');
$order_banner = new weTagData_choiceAttribute('order', array(new weTagDataOption('random()'),
	new weTagDataOption('Path'),
	new weTagDataOption('Clicks'),
	new weTagDataOption('Views'),
	new weTagDataOption('Rate'),
	), false, false, '');
$order_customer = (defined('CUSTOMER_TABLE') ? new weTagData_choiceAttribute('order', array(new weTagDataOption('random()'),
		new weTagDataOption('ID'),
		new weTagDataOption('Username'),
		new weTagDataOption('Forename'),
		new weTagDataOption('Surname'),
		), false, false, 'customer') : null);
$order_onlinemonitor = new weTagData_choiceAttribute('order', array(new weTagDataOption('random()'),
	new weTagDataOption('WebUserID'),
	new weTagDataOption('WebUserGroup'),
	new weTagDataOption('WebUserDescription'),
	new weTagDataOption('PageID'),
	new weTagDataOption('ObjectID'),
	new weTagDataOption('LastLogin'),
	new weTagDataOption('LastAccess'),
	), false, false, '');
$order_languagelink = new weTagData_choiceAttribute('order', array(new weTagDataOption('random()'),
	new weTagDataOption('Locale'),
	), false, false, '');
$desc = new weTagData_selectAttribute('desc', array(new weTagDataOption('true'),
	), false, '');
$offset = new weTagData_textAttribute('offset', false, '');
$casesensitive = new weTagData_selectAttribute('casesensitive', weTagData_selectAttribute::getTrueFalse(), false, '');
$classid = (defined('OBJECT_TABLE') ? new weTagData_selectorAttribute('classid', OBJECT_TABLE, 'object', false, '') : null);
$condition = new weTagData_textAttribute('condition', false, '');
$triggerid = new weTagData_selectorAttribute('triggerid', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$seeMode = new weTagData_selectAttribute('seeMode', weTagData_selectAttribute::getTrueFalse(), false, '');
$custBanner = (defined('CUSTOMER_TABLE') ? new weTagData_selectAttribute('customer', weTagData_selectAttribute::getTrueFalse(), false, 'customer') : null);
$workspaceID_document = new weTagData_multiSelectorAttribute('workspaceID', FILE_TABLE, weTagData_selectorAttribute::FOLDER, 'ID', false, '');
$workspaceID_object = defined('OBJECT_FILES_TABLE') ? new weTagData_multiSelectorAttribute('workspaceID', OBJECT_FILES_TABLE, weTagData_selectorAttribute::FOLDER, 'ID', false, '') : null;
$categoryids = new weTagData_multiSelectorAttribute('categoryids', CATEGORY_TABLE, '', 'ID', false, '');
$parentid = new weTagData_selectorAttribute('parentid', CATEGORY_TABLE, '', false, '');
$parentidname = new weTagData_textAttribute('parentidname', false, '');
$contenttypes = new weTagData_choiceAttribute('contenttypes', array(new weTagDataOption(we_base_ContentTypes::WEDOCUMENT),
	new weTagDataOption('image/*'),
	new weTagDataOption('text/html'),
	new weTagDataOption('text/plain'),
	new weTagDataOption('text/xml'),
	new weTagDataOption('text/js'),
	new weTagDataOption('text/css'),
	new weTagDataOption('application/*'),
	new weTagDataOption('application/x-shockwave-flash'),
	), false, true, '');
$searchable = new weTagData_selectAttribute('searchable', weTagData_selectAttribute::getTrueFalse(), false, '');
$defaultname = new weTagData_textAttribute('defaultname', false, '');
$documentid = new weTagData_selectorAttribute('documentid', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$collectionid = (defined('VFILE_TABLE') ? new weTagData_selectorAttribute('id', VFILE_TABLE, we_base_ContentTypes::COLLECTION, false, '') : null);
$objectid = (defined('OBJECT_FILES_TABLE') ? new weTagData_selectorAttribute('objectid', OBJECT_FILES_TABLE, 'objectFile', false, '') : null);
$calendar = new weTagData_selectAttribute('calendar', array(new weTagDataOption('year'),
	new weTagDataOption('month'),
	new weTagDataOption('month_table'),
	new weTagDataOption('day'),
	), false, '');
$datefield = new weTagData_textAttribute('datefield', false, '');
$date = new weTagData_textAttribute('date', false, '');
$weekstart = new weTagData_selectAttribute('weekstart', array(new weTagDataOption('sunday'),
	new weTagDataOption('monday'),
	new weTagDataOption('tuesday'),
	new weTagDataOption('wednesday'),
	new weTagDataOption('thursday'),
	new weTagDataOption('friday'),
	new weTagDataOption('saturday'),
	), false, '');
$cfilter = (defined('CUSTOMER_TABLE') ? new weTagData_selectAttribute('cfilter', array(new weTagDataOption('false'),
		new weTagDataOption('true'),
		new weTagDataOption('auto'),
		), false, 'customer') : null);
$recursive = new weTagData_selectAttribute('recursive', weTagData_selectAttribute::getTrueFalse(), false, '');
$docid = new weTagData_multiSelectorAttribute('docid', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, 'ID', false, '');
$customer = (defined('CUSTOMER_TABLE') ? new weTagData_textAttribute('customer', false, 'customer') : null);
$customers = (defined('CUSTOMER_TABLE') ? new weTagData_textAttribute('customers', false, 'customer') : null);
$id = new weTagData_textAttribute('id', false, '');
$predefinedSQL = new weTagData_textAttribute('predefinedSQL', false, '');
$numorder = new weTagData_selectAttribute('numorder', weTagData_selectAttribute::getTrueFalse(), false, '');
$locales = [];
foreach($GLOBALS["weFrontendLanguages"] as $lv){
	$locales[] = new weTagDataOption($lv);
}
$locales[] = new weTagDataOption('self');
$locales[] = new weTagDataOption('top');
$languages = new weTagData_choiceAttribute('languages', $locales, false, true, '');
$lastaccesslimit = new weTagData_textAttribute('lastaccesslimit', false, '');
$lastloginlimit = new weTagData_textAttribute('lastloginlimit', false, '');
$objectseourls = new weTagData_selectAttribute('objectseourls', weTagData_selectAttribute::getTrueFalse(), false, '');
$hidedirindex = new weTagData_selectAttribute('hidedirindex', weTagData_selectAttribute::getTrueFalse(), false, '');
$pagelanguage = new weTagData_choiceAttribute('pagelanguage', $locales, false, true, '');
$doc = new weTagData_selectAttribute('doc', array(new weTagDataOption('self'),
	new weTagDataOption('top'),
	), false, '');
$showself = new weTagData_selectAttribute('showself', weTagData_selectAttribute::getTrueFalse(), false, '');
$orderid = new weTagData_textAttribute('orderid', false, '');

$this->TypeAttribute = new weTagData_typeAttribute('type', array(
	new weTagDataOption('-', false, '', [], []),
	new weTagDataOption('document', false, '', array($name, $doctype, $categories, $catOr, $rows, $cols, $order_document, $desc, $offset, $languages, $searchable, $workspaceID_document, $cfilter, $recursive, $customers, $contenttypes, $id, $calendar, $numorder, $categoryids, $condition, $hidedirindex), []),
	new weTagDataOption('search', false, '', array($name, $doctype, $categories, $catOr, $languages, $rows, $cols, $order_search, $desc, $casesensitive, $classid, $workspaceID_document, $cfilter, $numorder, $triggerid, $objectseourls, $hidedirindex), []),
	new weTagDataOption('category', false, '', array($name, $categories, $rows, $cols, $order_category, $desc, $offset, $parentid, $parentidname, $categoryids), []),
	new weTagDataOption('object', false, '', array($name, $categories, $catOr, $rows, $cols, $order_document, $desc, $offset, $classid, $condition, $triggerid, $languages, $searchable, $workspaceID_object, $cfilter, $docid, $customers, $id, $calendar, $predefinedSQL, $categoryids, $objectseourls, $hidedirindex), []),
	new weTagDataOption('multiobject', false, '', array($name, $categories, $catOr, $rows, $cols, $order_document, $desc, $offset, $classid, $condition, $triggerid, $languages, $searchable, $cfilter, $calendar, $objectseourls, $hidedirindex), []),
	new weTagDataOption('collectionitems', false, 'collection', array($collectionid, $name, $doctype, $categories, $catOr, $rows, $cols, $order_document, $desc, $offset, $languages, $searchable, $workspaceID_document, $cfilter, $recursive, $customers, $contenttypes, $calendar, $numorder, $categoryids, $condition, $hidedirindex), []),
	new weTagDataOption('banner', false, 'banner', array($name, $rows, $cols, $order_banner, $custBanner), []),
	new weTagDataOption('variant', false, '', array($name, $defaultname, $documentid, $objectid, $objectseourls, $hidedirindex), []),
	new weTagDataOption('customer', false, 'customer', array($name, $rows, $cols, $order_customer, $desc, $offset, $condition, $docid), []),
	new weTagDataOption('onlinemonitor', false, 'customer', array($name, $rows, $cols, $order_onlinemonitor, $desc, $offset, $condition, $docid, $lastaccesslimit, $lastloginlimit), []),
	new weTagDataOption('languagelink', false, '', array($name, $rows, $cols, $order_languagelink, $desc, $offset, $pagelanguage, $showself, $objectseourls, $hidedirindex), []),
	new weTagDataOption('order', false, '', array($name, $rows, $cols, $order_document, $desc, $offset, $condition, $docid), []),
	new weTagDataOption('orderitem', false, 'shop', array($name, $rows, $cols, $order_document, $desc, $offset, $condition, $docid, $orderid), []),
	), false, '');

$this->Attributes = array($MultiSelector, $collectionid, $name, $doctype, $categories, $catOr, $rows, $cols, $order_document, $order_search, $order_category,
	$order_banner, $order_customer, $order_onlinemonitor, $order_languagelink, $orderid, $desc, $offset, $casesensitive, $classid, $condition, $triggerid, $seeMode,
	$workspaceID_document, $workspaceID_object, $categoryids, $parentid, $parentidname, $contenttypes, $searchable, $defaultname, $documentid, $objectid,
	$datefield, $date, $weekstart, $cfilter, $recursive, $docid, $customer, $customers, $custBanner, $id, $calendar, $predefinedSQL, $numorder, $languages, $lastaccesslimit,
	$lastloginlimit, $objectseourls, $hidedirindex, $pagelanguage, $doc, $showself);
