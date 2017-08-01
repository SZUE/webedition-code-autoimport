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

$name = new we_tagData_choiceAttribute('name', [new we_tagData_option('WE_Path'),
	new we_tagData_option('WE_ID'),
	new we_tagData_option('WE_Text'),
	new we_tagData_option('WE_URL'),
	new we_tagData_option('WE_Customer_ID'),
	new we_tagData_option('WE_TriggerID'),
	new we_tagData_option('WE_CreationDate'),
	new we_tagData_option('WE_ModDate'),
	new we_tagData_option('WE_Published'),
	new we_tagData_option('WE_ParentID'),
	new we_tagData_option('WE_Text'),
	new we_tagData_option('WE_Category'),
	new we_tagData_option('WE_SHOPVARIANTS', false, 'shop'),
		], false, false, '');
$classid = (defined('OBJECT_TABLE') ? new we_tagData_selectorAttribute('classid', OBJECT_TABLE, 'object', false, '') : null);
$hyperlink = new we_tagData_selectAttribute('hyperlink', we_tagData_selectAttribute::getTrueFalse(), false, '');
$tid = (defined('TEMPLATES_TABLE') ? new we_tagData_selectorAttribute('tid', TEMPLATES_TABLE, we_base_ContentTypes::TEMPLATE, false, '') : null);
$href = new we_tagData_textAttribute('href', false, '');
$target = new we_tagData_choiceAttribute('target', [new we_tagData_option('_top'),
	new we_tagData_option('_parent'),
	new we_tagData_option('_self'),
	new we_tagData_option('_blank'),
		], false, false, '');
$class = new we_tagData_textAttribute('class', false, '');
$style = new we_tagData_textAttribute('style', false, '');
$format = new we_tagData_textAttribute('format', false, '');
$num_format = new we_tagData_choiceAttribute('num_format', [new we_tagData_option('german'),
	new we_tagData_option('french'),
	new we_tagData_option('english'),
	new we_tagData_option('swiss'),
		], false, false, '');
$thumbnail = new we_tagData_sqlRowAttribute('thumbnail', THUMBNAILS_TABLE, false, 'Name', '', '', '');
$id = (defined('FILE_TABLE') ? new we_tagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '') : null);
$parentidname = new we_tagData_textAttribute('parentidname', false, '');
$winprops = new we_tagData_textAttribute('winprops', false, '');
$alt = new we_tagData_textAttribute('alt', false, '');
$max = new we_tagData_textAttribute('max', false, '');
$src = new we_tagData_textAttribute('src', false, '');
$width = new we_tagData_textAttribute('width', false, '');
$height = new we_tagData_textAttribute('height', false, '');
$border = new we_tagData_textAttribute('border', false, '');
$hspace = new we_tagData_textAttribute('hspace', false, '');
$vspace = new we_tagData_textAttribute('vspace', false, '');
$align = new we_tagData_selectAttribute('align', [new we_tagData_option('left'),
	new we_tagData_option('right'),
	new we_tagData_option('top'),
	new we_tagData_option('bottom'),
	new we_tagData_option('absmiddle'),
	new we_tagData_option('middle'),
	new we_tagData_option('texttop'),
	new we_tagData_option('baseline'),
	new we_tagData_option('absbottom'),
		], false, '');
//$only = new weTagData_textAttribute('only', false, '');
$htmlspecialchars = new we_tagData_selectAttribute('htmlspecialchars', we_tagData_selectAttribute::getTrueFalse(), false, '');
$seeMode = new we_tagData_selectAttribute('seeMode', we_tagData_selectAttribute::getTrueFalse(), false, '');
$xml = new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, '');
$win2iso = new we_tagData_selectAttribute('win2iso', we_tagData_selectAttribute::getTrueFalse(), false, '');
$listviewname = new we_tagData_textAttribute('listviewname', false, '');
$striphtml = new we_tagData_selectAttribute('striphtml', [new we_tagData_option('false'),
	new we_tagData_option('true'),
		], false, '');
$only = new we_tagData_selectAttribute('only', [new we_tagData_option('name'),
	new we_tagData_option('src'),
	new we_tagData_option('parentpath'),
	new we_tagData_option('filename'),
	new we_tagData_option('extension'),
	new we_tagData_option('filesize'),
	new we_tagData_option('id'),
		], false, '');
$onlyImg = new we_tagData_selectAttribute('only', [new we_tagData_option('name'),
	new we_tagData_option('src'),
	new we_tagData_option('parentpath'),
	new we_tagData_option('filename'),
	new we_tagData_option('extension'),
	new we_tagData_option('filesize'),
	new we_tagData_option('width'),
	new we_tagData_option('height'),
	new we_tagData_option('alt'),
		], false, '');
$outputlanguage = new we_tagData_textAttribute('outputlanguage', false, '');
$doc = new we_tagData_selectAttribute('doc', [new we_tagData_option('self'),
	new we_tagData_option('top'),
		], false, '');
$triggerid = (defined('FILE_TABLE') ? new we_tagData_selectorAttribute('triggerid', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '') : null);
$usekey = new we_tagData_selectAttribute('usekey', we_tagData_selectAttribute::getTrueFalse(), false, '');
$showpath = new we_tagData_selectAttribute('showpath', we_tagData_selectAttribute::getTrueFalse(), false, '');
$rootdir = new we_tagData_textAttribute('rootdir', false, '');
$catfield = new we_tagData_selectAttribute('field', [new we_tagData_option('id'),
	new we_tagData_option('category'),
	new we_tagData_option('path'),
	new we_tagData_option('title'),
	new we_tagData_option('description'),
	new we_tagData_option('is_destinationprinciple'),
	new we_tagData_option('is_from doc_object'),
	new we_tagData_option('is_fallback_to_standard'),
	new we_tagData_option('is_fallback_to_active')
		], false, '');
$vatfield = new we_tagData_selectAttribute('field', [new we_tagData_option('id'),
	new we_tagData_option('vat'),
	new we_tagData_option('name'),
	new we_tagData_option('country'),
	new we_tagData_option('countrycode'),
	new we_tagData_option('is_standard'),
	new we_tagData_option('is_fallback_to_standard'),
	new we_tagData_option('is_fallback_to_prefs'),
	new we_tagData_option('is_country_fallback_to_prefs')
		], false, '');
$this->TypeAttribute = new we_tagData_typeAttribute('type', [
	new we_tagData_option('-'),
	new we_tagData_option('text', false, '', [$name, $hyperlink, $href, $target, $num_format, $alt, $max, $striphtml, $htmlspecialchars, $triggerid], [$name]),
	new we_tagData_option('date', false, '', [$name, $hyperlink, $href, $target, $format, $alt, $max, $htmlspecialchars, $triggerid], [$name]),
	new we_tagData_option('img', false, '', [$name, $hyperlink, $href, $target, $thumbnail, $src, $width, $height, $border, $hspace, $vspace, $align, $onlyImg, $triggerid], [$name]),
	new we_tagData_option('flashmovie', false, '', [$name, $width, $height, $triggerid], [$name]),
	new we_tagData_option('href', false, '', [$name], [$name]),
	new we_tagData_option('link', false, '', [$name], [$name]),
	new we_tagData_option('day', false, '', [], []),
	new we_tagData_option('dayname_long', false, '', [$outputlanguage], []),
	new we_tagData_option('dayname_short', false, '', [$outputlanguage], []),
	new we_tagData_option('week', false, '', [], []),
	new we_tagData_option('month', false, '', [], []),
	new we_tagData_option('monthname_long', false, '', [$outputlanguage], []),
	new we_tagData_option('monthname_short', false, '', [$outputlanguage], []),
	new we_tagData_option('year', false, '', [], []),
	new we_tagData_option('select', false, 'object', [$name, $usekey, $htmlspecialchars, $triggerid], [$name]),
	new we_tagData_option('binary', false, 'object', [$name, $hyperlink, $href, $target, $only], [$name]),
	new we_tagData_option('float', false, '', [$name, $hyperlink, $href, $target, $num_format, $triggerid], [$name]),
	new we_tagData_option('int', false, 'object', [$name, $hyperlink, $href, $target, $triggerid], [$name]),
	new we_tagData_option('collection', false, '', [$name], [$name]),
	new we_tagData_option('shopVat', false, '', [$vatfield], []),
	new we_tagData_option('shopCategory', false, '', [$catfield, $showpath, $rootdir], []),
	new we_tagData_option('checkbox', false, '', [$name], [$name]),
	new we_tagData_option('country', false, '', [$outputlanguage, $doc], []),
	new we_tagData_option('language', false, '', [$outputlanguage, $doc], [])
		], false, '');

$this->Attributes = [$name, $classid, $hyperlink, $tid, $href, $target, $class, $style, $format, $num_format, $thumbnail, $id, $parentidname, $winprops, $alt, $max, $src,
	$width, $height, $border, $hspace, $vspace, $align, $only, $onlyImg, $htmlspecialchars, $seeMode, $xml, $win2iso, $listviewname, $striphtml, $outputlanguage, $doc, $triggerid,
	$usekey, $vatfield, $catfield, $showpath, $rootdir];

