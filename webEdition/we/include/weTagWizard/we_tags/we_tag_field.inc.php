<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$name = new weTagData_choiceAttribute('name', [new weTagDataOption('WE_Path'),
	new weTagDataOption('WE_ID'),
	new weTagDataOption('WE_Text'),
	new weTagDataOption('WE_URL'),
	new weTagDataOption('WE_Customer_ID'),
	new weTagDataOption('WE_TriggerID'),
	new weTagDataOption('WE_CreationDate'),
	new weTagDataOption('WE_ModDate'),
	new weTagDataOption('WE_Published'),
	new weTagDataOption('WE_ParentID'),
	new weTagDataOption('WE_Text'),
	new weTagDataOption('WE_Category'),
	new weTagDataOption('WE_SHOPVARIANTS', false, 'shop'),
		], false, false, '');
$classid = (defined('OBJECT_TABLE') ? new weTagData_selectorAttribute('classid', OBJECT_TABLE, 'object', false, '') : null);
$hyperlink = new weTagData_selectAttribute('hyperlink', weTagData_selectAttribute::getTrueFalse(), false, '');
$tid = (defined('TEMPLATES_TABLE') ? new weTagData_selectorAttribute('tid', TEMPLATES_TABLE, 'text/weTmpl', false, '') : null);
$href = new weTagData_textAttribute('href', false, '');
$target = new weTagData_choiceAttribute('target', [new weTagDataOption('_top'),
	new weTagDataOption('_parent'),
	new weTagDataOption('_self'),
	new weTagDataOption('_blank'),
		], false, false, '');
$class = new weTagData_textAttribute('class', false, '');
$style = new weTagData_textAttribute('style', false, '');
$format = new weTagData_textAttribute('format', false, '');
$num_format = new weTagData_choiceAttribute('num_format', [new weTagDataOption('german'),
	new weTagDataOption('french'),
	new weTagDataOption('english'),
	new weTagDataOption('swiss'),
		], false, false, '');
$thumbnail = new weTagData_sqlRowAttribute('thumbnail', THUMBNAILS_TABLE, false, 'Name', '', '', '');
$id = (defined('FILE_TABLE') ? new weTagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '') : null);
$parentidname = new weTagData_textAttribute('parentidname', false, '');
$winprops = new weTagData_textAttribute('winprops', false, '');
$alt = new weTagData_textAttribute('alt', false, '');
$max = new weTagData_textAttribute('max', false, '');
$src = new weTagData_textAttribute('src', false, '');
$width = new weTagData_textAttribute('width', false, '');
$height = new weTagData_textAttribute('height', false, '');
$border = new weTagData_textAttribute('border', false, '');
$hspace = new weTagData_textAttribute('hspace', false, '');
$vspace = new weTagData_textAttribute('vspace', false, '');
$align = new weTagData_selectAttribute('align', [new weTagDataOption('left'),
	new weTagDataOption('right'),
	new weTagDataOption('top'),
	new weTagDataOption('bottom'),
	new weTagDataOption('absmiddle'),
	new weTagDataOption('middle'),
	new weTagDataOption('texttop'),
	new weTagDataOption('baseline'),
	new weTagDataOption('absbottom'),
		], false, '');
//$only = new weTagData_textAttribute('only', false, '');
$htmlspecialchars = new weTagData_selectAttribute('htmlspecialchars', weTagData_selectAttribute::getTrueFalse(), false, '');
$seeMode = new weTagData_selectAttribute('seeMode', weTagData_selectAttribute::getTrueFalse(), false, '');
$xml = new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, '');
$win2iso = new weTagData_selectAttribute('win2iso', weTagData_selectAttribute::getTrueFalse(), false, '');
$listviewname = new weTagData_textAttribute('listviewname', false, '');
$striphtml = new weTagData_selectAttribute('striphtml', [new weTagDataOption('false'),
	new weTagDataOption('true'),
		], false, '');
$only = new weTagData_selectAttribute('only', [new weTagDataOption('name'),
	new weTagDataOption('src'),
	new weTagDataOption('parentpath'),
	new weTagDataOption('filename'),
	new weTagDataOption('extension'),
	new weTagDataOption('filesize'),
	new weTagDataOption('id'),
		], false, '');
$onlyImg = new weTagData_selectAttribute('only', [new weTagDataOption('name'),
	new weTagDataOption('src'),
	new weTagDataOption('parentpath'),
	new weTagDataOption('filename'),
	new weTagDataOption('extension'),
	new weTagDataOption('filesize'),
	new weTagDataOption('width'),
	new weTagDataOption('height'),
	new weTagDataOption('alt'),
		], false, '');
$outputlanguage = new weTagData_textAttribute('outputlanguage', false, '');
$doc = new weTagData_selectAttribute('doc', [new weTagDataOption('self'),
	new weTagDataOption('top'),
		], false, '');
$triggerid = (defined('FILE_TABLE') ? new weTagData_selectorAttribute('triggerid', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '') : null);
$usekey = new weTagData_selectAttribute('usekey', weTagData_selectAttribute::getTrueFalse(), false, '');
$showpath = new weTagData_selectAttribute('showpath', weTagData_selectAttribute::getTrueFalse(), false, '');
$rootdir = new weTagData_textAttribute('rootdir', false, '');
$catfield = new weTagData_selectAttribute('field', [new weTagDataOption('id'),
	new weTagDataOption('category'),
	new weTagDataOption('path'),
	new weTagDataOption('title'),
	new weTagDataOption('description'),
	new weTagDataOption('is_destinationprinciple'),
	new weTagDataOption('is_from doc_object'),
	new weTagDataOption('is_fallback_to_standard'),
	new weTagDataOption('is_fallback_to_active')
		], false, '');
$vatfield = new weTagData_selectAttribute('field', [new weTagDataOption('id'),
	new weTagDataOption('vat'),
	new weTagDataOption('name'),
	new weTagDataOption('country'),
	new weTagDataOption('countrycode'),
	new weTagDataOption('is_standard'),
	new weTagDataOption('is_fallback_to_standard'),
	new weTagDataOption('is_fallback_to_prefs'),
	new weTagDataOption('is_country_fallback_to_prefs')
		], false, '');
$this->TypeAttribute = new weTagData_typeAttribute('type', [
	new weTagDataOption('-'),
	new weTagDataOption('text', false, '', [$name, $hyperlink, $href, $target, $num_format, $alt, $max, $striphtml, $htmlspecialchars, $triggerid], [$name]),
	new weTagDataOption('date', false, '', [$name, $hyperlink, $href, $target, $format, $alt, $max, $htmlspecialchars, $triggerid], [$name]),
	new weTagDataOption('img', false, '', [$name, $hyperlink, $href, $target, $thumbnail, $src, $width, $height, $border, $hspace, $vspace, $align, $onlyImg, $triggerid], [$name]),
	new weTagDataOption('flashmovie', false, '', [$name, $width, $height, $triggerid], [$name]),
	new weTagDataOption('href', false, '', [$name], [$name]),
	new weTagDataOption('link', false, '', [$name], [$name]),
	new weTagDataOption('day', false, '', [], []),
	new weTagDataOption('dayname_long', false, '', [$outputlanguage], []),
	new weTagDataOption('dayname_short', false, '', [$outputlanguage], []),
	new weTagDataOption('week', false, '', [], []),
	new weTagDataOption('month', false, '', [], []),
	new weTagDataOption('monthname_long', false, '', [$outputlanguage], []),
	new weTagDataOption('monthname_short', false, '', [$outputlanguage], []),
	new weTagDataOption('year', false, '', [], []),
	new weTagDataOption('select', false, 'object', [$name, $usekey, $htmlspecialchars, $triggerid], [$name]),
	new weTagDataOption('binary', false, 'object', [$name, $hyperlink, $href, $target, $only], [$name]),
	new weTagDataOption('float', false, '', [$name, $hyperlink, $href, $target, $num_format, $triggerid], [$name]),
	new weTagDataOption('int', false, 'object', [$name, $hyperlink, $href, $target, $triggerid], [$name]),
	new weTagDataOption('collection', false, '', [$name], [$name]),
	new weTagDataOption('shopVat', false, '', [$vatfield], []),
	new weTagDataOption('shopCategory', false, '', [$catfield, $showpath, $rootdir], []),
	new weTagDataOption('checkbox', false, '', [$name], [$name]),
	new weTagDataOption('country', false, '', [$outputlanguage, $doc], []),
	new weTagDataOption('language', false, '', [$outputlanguage, $doc], [])
		], false, '');

$this->Attributes = [$name, $classid, $hyperlink, $tid, $href, $target, $class, $style, $format, $num_format, $thumbnail, $id, $parentidname, $winprops, $alt, $max, $src,
	$width, $height, $border, $hspace, $vspace, $align, $only, $onlyImg, $htmlspecialchars, $seeMode, $xml, $win2iso, $listviewname, $striphtml, $outputlanguage, $doc, $triggerid,
	$usekey, $vatfield, $catfield, $showpath, $rootdir];

