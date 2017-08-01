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
$this->DefaultValue = '<we:repeat>
<we:field name="Title" alt="we_path" hyperlink="true"/>
</we:repeat>';

$MultiSelector = new we_tagData_multiSelectorAttribute('MultiSelector', FILE_TABLE, '', '', false, '');
$name = new we_tagData_textAttribute('name', false, '');
$doctype = new we_tagData_sqlRowAttribute('doctype', DOC_TYPES_TABLE, false, 'DocType', '', '', '');
$categories = new we_tagData_multiSelectorAttribute('categories', CATEGORY_TABLE, '', 'Path', false, '');
$catOr = new we_tagData_selectAttribute('catOr', [new we_tagData_option('true'),
	], false, '');
$rows = new we_tagData_textAttribute('rows', false, '');
$cols = new we_tagData_textAttribute('cols', false, '');
$order_document = new we_tagData_choiceAttribute('order', [new we_tagData_option('random()'),
	new we_tagData_option('WE_ID'),
	new we_tagData_option('WE_Filename'),
	new we_tagData_option('WE_Creationdate'),
	new we_tagData_option('WE_Moddate'),
	new we_tagData_option('WE_Published'),
	], false, false, '');
$order_search = new we_tagData_choiceAttribute('order', [new we_tagData_option('random()'),
	new we_tagData_option('WE_ID'),
	new we_tagData_option('WE_Filename'),
	new we_tagData_option('WE_Creationdate'),
	new we_tagData_option('WE_Moddate'),
	new we_tagData_option('Title'),
	new we_tagData_option('Description'),
	new we_tagData_option('Path'),
	new we_tagData_option('Text'),
	new we_tagData_option('DID'),
	new we_tagData_option('OID'),
	new we_tagData_option('ID'),
	], false, false, '');
$order_category = new we_tagData_choiceAttribute('order', [new we_tagData_option('random()'),
	new we_tagData_option('ID'),
	new we_tagData_option('Category'),
	new we_tagData_option('Text'),
	new we_tagData_option('Path'),
	], false, false, '');
$order_banner = new we_tagData_choiceAttribute('order', [new we_tagData_option('random()'),
	new we_tagData_option('Path'),
	new we_tagData_option('Clicks'),
	new we_tagData_option('Views'),
	new we_tagData_option('Rate'),
	], false, false, '');
$order_customer = (defined('CUSTOMER_TABLE') ? new we_tagData_choiceAttribute('order', [new we_tagData_option('random()'),
		new we_tagData_option('ID'),
		new we_tagData_option('Username'),
		new we_tagData_option('Forename'),
		new we_tagData_option('Surname'),
		], false, false, 'customer') : null);
$order_onlinemonitor = new we_tagData_choiceAttribute('order', [new we_tagData_option('random()'),
	new we_tagData_option('WebUserID'),
	new we_tagData_option('WebUserGroup'),
	new we_tagData_option('WebUserDescription'),
	new we_tagData_option('PageID'),
	new we_tagData_option('ObjectID'),
	new we_tagData_option('LastLogin'),
	new we_tagData_option('LastAccess'),
	], false, false, '');
$order_languagelink = new we_tagData_choiceAttribute('order', [new we_tagData_option('random()'),
	new we_tagData_option('Locale'),
	], false, false, '');
$desc = new we_tagData_selectAttribute('desc', [new we_tagData_option('true'),
	], false, '');
$offset = new we_tagData_textAttribute('offset', false, '');
$casesensitive = new we_tagData_selectAttribute('casesensitive', we_tagData_selectAttribute::getTrueFalse(), false, '');
$classid = (defined('OBJECT_TABLE') ? new we_tagData_selectorAttribute('classid', OBJECT_TABLE, 'object', false, '') : null);
$condition = new we_tagData_textAttribute('condition', false, '');
$triggerid = new we_tagData_selectorAttribute('triggerid', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$seeMode = new we_tagData_selectAttribute('seeMode', we_tagData_selectAttribute::getTrueFalse(), false, '');
$custBanner = (defined('CUSTOMER_TABLE') ? new we_tagData_selectAttribute('customer', we_tagData_selectAttribute::getTrueFalse(), false, 'customer') : null);
$workspaceID_document = new we_tagData_multiSelectorAttribute('workspaceID', FILE_TABLE, we_tagData_selectorAttribute::FOLDER, 'ID', false, '');
$workspaceID_object = defined('OBJECT_FILES_TABLE') ? new we_tagData_multiSelectorAttribute('workspaceID', OBJECT_FILES_TABLE, we_tagData_selectorAttribute::FOLDER, 'ID', false, '') : null;
$categoryids = new we_tagData_multiSelectorAttribute('categoryids', CATEGORY_TABLE, '', 'ID', false, '');
$parentid = new we_tagData_selectorAttribute('parentid', CATEGORY_TABLE, '', false, '');
$parentidname = new we_tagData_textAttribute('parentidname', false, '');
$contenttypes = new we_tagData_choiceAttribute('contenttypes', [
	new we_tagData_option(we_base_ContentTypes::WEDOCUMENT),
	new we_tagData_option(we_base_ContentTypes::IMAGE),
	new we_tagData_option(we_base_ContentTypes::HTML),
	new we_tagData_option(we_base_ContentTypes::TEXT),
	new we_tagData_option(we_base_ContentTypes::XML),
	new we_tagData_option(we_base_ContentTypes::JS),
	new we_tagData_option(we_base_ContentTypes::CSS),
	new we_tagData_option(we_base_ContentTypes::APPLICATION),
	new we_tagData_option(we_base_ContentTypes::FLASH),
	], false, true, '');
$searchable = new we_tagData_selectAttribute('searchable', we_tagData_selectAttribute::getTrueFalse(), false, '');
$defaultname = new we_tagData_textAttribute('defaultname', false, '');
$documentid = new we_tagData_selectorAttribute('documentid', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$collectionid = (defined('VFILE_TABLE') ? new we_tagData_selectorAttribute('id', VFILE_TABLE, we_base_ContentTypes::COLLECTION, false, '') : null);
$objectid = (defined('OBJECT_FILES_TABLE') ? new we_tagData_selectorAttribute('objectid', OBJECT_FILES_TABLE, we_base_ContentTypes::OBJECT_FILE, false, '') : null);
$calendar = new we_tagData_selectAttribute('calendar', [new we_tagData_option('year'),
	new we_tagData_option('month'),
	new we_tagData_option('month_table'),
	new we_tagData_option('day'),
	], false, '');
$datefield = new we_tagData_textAttribute('datefield', false, '');
$date = new we_tagData_textAttribute('date', false, '');
$weekstart = new we_tagData_selectAttribute('weekstart', [new we_tagData_option('sunday'),
	new we_tagData_option('monday'),
	new we_tagData_option('tuesday'),
	new we_tagData_option('wednesday'),
	new we_tagData_option('thursday'),
	new we_tagData_option('friday'),
	new we_tagData_option('saturday'),
	], false, '');
$cfilter = (defined('CUSTOMER_TABLE') ? new we_tagData_selectAttribute('cfilter', [new we_tagData_option('false'),
		new we_tagData_option('true'),
		], false, 'customer') : null);
$recursive = new we_tagData_selectAttribute('recursive', we_tagData_selectAttribute::getTrueFalse(), false, '');
$docid = new we_tagData_multiSelectorAttribute('docid', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, 'ID', false, '');
$customer = (defined('CUSTOMER_TABLE') ? new we_tagData_textAttribute('customer', false, 'customer') : null);
$customers = (defined('CUSTOMER_TABLE') ? new we_tagData_textAttribute('customers', false, 'customer') : null);
$id = new we_tagData_textAttribute('id', false, '');
$predefinedSQL = new we_tagData_textAttribute('predefinedSQL', false, '');
$numorder = new we_tagData_selectAttribute('numorder', we_tagData_selectAttribute::getTrueFalse(), false, '');
$locales = [];
foreach($GLOBALS['weFrontendLanguages'] as $lv){
	$locales[] = new we_tagData_option($lv);
}
$locales[] = new we_tagData_option('self');
$locales[] = new we_tagData_option('top');
$languages = new we_tagData_choiceAttribute('languages', $locales, false, true, '');
$lastaccesslimit = new we_tagData_textAttribute('lastaccesslimit', false, '');
$lastloginlimit = new we_tagData_textAttribute('lastloginlimit', false, '');
$objectseourls = new we_tagData_selectAttribute('objectseourls', we_tagData_selectAttribute::getTrueFalse(), false, '');
$hidedirindex = new we_tagData_selectAttribute('hidedirindex', we_tagData_selectAttribute::getTrueFalse(), false, '');
$pagelanguage = new we_tagData_choiceAttribute('pagelanguage', $locales, false, true, '');
$doc = new we_tagData_selectAttribute('doc', [new we_tagData_option('self'),
	new we_tagData_option('top'),
	], false, '');
$showself = new we_tagData_selectAttribute('showself', we_tagData_selectAttribute::getTrueFalse(), false, '');
$orderid = new we_tagData_textAttribute('orderid', false, '');

$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('-'),
	new we_tagData_option('document', false, '', [$name, $doctype, $categories, $catOr, $rows, $cols, $order_document, $desc, $offset, $languages, $searchable, $workspaceID_document, $cfilter, $recursive, $customers, $contenttypes, $id, $calendar, $numorder, $categoryids, $condition, $hidedirindex], []),
	new we_tagData_option('search', false, '', [$name, $doctype, $categories, $catOr, $languages, $rows, $cols, $order_search, $desc, $casesensitive, $classid, $workspaceID_document, $cfilter, $numorder, $triggerid, $objectseourls, $hidedirindex], []),
	new we_tagData_option('category', false, '', [$name, $categories, $rows, $cols, $order_category, $desc, $offset, $parentid, $parentidname, $categoryids], []),
	new we_tagData_option('object', false, '', [$name, $categories, $catOr, $rows, $cols, $order_document, $desc, $offset, $classid, $condition, $triggerid, $languages, $searchable, $workspaceID_object, $cfilter, $docid, $customers, $id, $calendar, $predefinedSQL, $categoryids, $objectseourls, $hidedirindex], []),
	new we_tagData_option('multiobject', false, '', [$name, $categories, $catOr, $rows, $cols, $order_document, $desc, $offset, $classid, $condition, $triggerid, $languages, $searchable, $cfilter, $calendar, $objectseourls, $hidedirindex], []),
	new we_tagData_option('collectionitems', false, 'collection', [$collectionid, $name, $doctype, $categories, $catOr, $rows, $cols, $order_document, $desc, $offset, $languages, $searchable, $workspaceID_document, $cfilter, $recursive, $customers, $contenttypes, $calendar, $numorder, $categoryids, $condition, $hidedirindex], []),
	new we_tagData_option('banner', false, 'banner', [$name, $rows, $cols, $order_banner, $custBanner], []),
	new we_tagData_option('variant', false, '', [$name, $defaultname, $documentid, $objectid, $objectseourls, $hidedirindex], []),
	new we_tagData_option('customer', false, 'customer', [$name, $rows, $cols, $order_customer, $desc, $offset, $condition, $docid], []),
	new we_tagData_option('onlinemonitor', false, 'customer', [$name, $rows, $cols, $order_onlinemonitor, $desc, $offset, $condition, $docid, $lastaccesslimit, $lastloginlimit], []),
	new we_tagData_option('languagelink', false, '', [$name, $rows, $cols, $order_languagelink, $desc, $offset, $pagelanguage, $showself, $objectseourls, $hidedirindex], []),
	new we_tagData_option('order', false, '', [$name, $rows, $cols, $order_document, $desc, $offset, $condition, $docid], []),
	new we_tagData_option('orderitem', false, 'shop', [$name, $rows, $cols, $order_document, $desc, $offset, $condition, $docid, $orderid], []),
	], false, '');

$this->Attributes = [$MultiSelector, $collectionid, $name, $doctype, $categories, $catOr, $rows, $cols, $order_document, $order_search, $order_category,
	$order_banner, $order_customer, $order_onlinemonitor, $order_languagelink, $orderid, $desc, $offset, $casesensitive, $classid, $condition, $triggerid, $seeMode,
	$workspaceID_document, $workspaceID_object, $categoryids, $parentid, $parentidname, $contenttypes, $searchable, $defaultname, $documentid, $objectid,
	$datefield, $date, $weekstart, $cfilter, $recursive, $docid, $customer, $customers, $custBanner, $id, $calendar, $predefinedSQL, $numorder, $languages, $lastaccesslimit,
	$lastloginlimit, $objectseourls, $hidedirindex, $pagelanguage, $doc, $showself];
