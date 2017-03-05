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

$name = new weTagData_choiceAttribute('name', array(new weTagDataOption('WE_PATH'),
	new weTagDataOption('WE_ID'),
	new weTagDataOption('WE_TEXT'),
	new weTagDataOption('WE_URL'),
	new weTagDataOption('WE_CUSTOMER_ID'),
	new weTagDataOption('WE_TRIGGERID'),
	new weTagDataOption('wedoc_CreationDate'),
	new weTagDataOption('wedoc_ModDate'),
	new weTagDataOption('wedoc_Published'),
	new weTagDataOption('wedoc_ParentID'),
	new weTagDataOption('wedoc_Text'),
	new weTagDataOption('wedoc_Category'),
	new weTagDataOption('WE_SHOPVARIANTS', false, 'shop'),
	), false, false, '');
$classid = (defined('OBJECT_TABLE') ? new weTagData_selectorAttribute('classid', OBJECT_TABLE, 'object', false, '') : null);
$hyperlink = new weTagData_selectAttribute('hyperlink', weTagData_selectAttribute::getTrueFalse(), false, '');
$tid = (defined('TEMPLATES_TABLE') ? new weTagData_selectorAttribute('tid', TEMPLATES_TABLE, 'text/weTmpl', false, '') : null);
$href = new weTagData_textAttribute('href', false, '');
$target = new weTagData_choiceAttribute('target', array(new weTagDataOption('_top'),
	new weTagDataOption('_parent'),
	new weTagDataOption('_self'),
	new weTagDataOption('_blank'),
	), false, false, '');
$class = new weTagData_textAttribute('class', false, '');
$style = new weTagData_textAttribute('style', false, '');
$format = new weTagData_textAttribute('format', false, '');
$num_format = new weTagData_choiceAttribute('num_format', array(new weTagDataOption('german'),
	new weTagDataOption('french'),
	new weTagDataOption('english'),
	new weTagDataOption('swiss'),
	), false, false, '');
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
$align = new weTagData_selectAttribute('align', array(new weTagDataOption('left'),
	new weTagDataOption('right'),
	new weTagDataOption('top'),
	new weTagDataOption('bottom'),
	new weTagDataOption('absmiddle'),
	new weTagDataOption('middle'),
	new weTagDataOption('texttop'),
	new weTagDataOption('baseline'),
	new weTagDataOption('absbottom'),
	), false, '');
//$only = new weTagData_textAttribute('only', false, '');
$htmlspecialchars = new weTagData_selectAttribute('htmlspecialchars', weTagData_selectAttribute::getTrueFalse(), false, '');
$seeMode = new weTagData_selectAttribute('seeMode', weTagData_selectAttribute::getTrueFalse(), false, '');
$xml = new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, '');
$win2iso = new weTagData_selectAttribute('win2iso', weTagData_selectAttribute::getTrueFalse(), false, '');
$listviewname = new weTagData_textAttribute('listviewname', false, '');
$striphtml = new weTagData_selectAttribute('striphtml', array(new weTagDataOption('false'),
	new weTagDataOption('true'),
	), false, '');
$only = new weTagData_selectAttribute('only', array(new weTagDataOption('name'),
	new weTagDataOption('src'),
	new weTagDataOption('parentpath'),
	new weTagDataOption('filename'),
	new weTagDataOption('extension'),
	new weTagDataOption('filesize'),
	new weTagDataOption('id'),
	), false, '');
$onlyImg = new weTagData_selectAttribute('only', array(new weTagDataOption('name'),
	new weTagDataOption('src'),
	new weTagDataOption('parentpath'),
	new weTagDataOption('filename'),
	new weTagDataOption('extension'),
	new weTagDataOption('filesize'),
	new weTagDataOption('width'),
	new weTagDataOption('height'),
	new weTagDataOption('alt'),
	), false, '');
$outputlanguage = new weTagData_textAttribute('outputlanguage', false, '');
$doc = new weTagData_selectAttribute('doc', array(new weTagDataOption('self'),
	new weTagDataOption('top'),
	), false, '');
$triggerid = (defined('FILE_TABLE') ? new weTagData_selectorAttribute('triggerid', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '') : null);
$usekey = new weTagData_selectAttribute('usekey', weTagData_selectAttribute::getTrueFalse(), false, '');
$showpath = new weTagData_selectAttribute('showpath', weTagData_selectAttribute::getTrueFalse(), false, '');
$rootdir = new weTagData_textAttribute('rootdir', false, '');
$catfield = new weTagData_selectAttribute('field', array(
	new weTagDataOption('id'),
	new weTagDataOption('category'),
	new weTagDataOption('path'),
	new weTagDataOption('title'),
	new weTagDataOption('description'),
	new weTagDataOption('is_destinationprinciple'),
	new weTagDataOption('is_from doc_object'),
	new weTagDataOption('is_fallback_to_standard'),
	new weTagDataOption('is_fallback_to_active')
	), false, '');
$vatfield = new weTagData_selectAttribute('field', array(
	new weTagDataOption('id'),
	new weTagDataOption('vat'),
	new weTagDataOption('name'),
	new weTagDataOption('country'),
	new weTagDataOption('countrycode'),
	new weTagDataOption('is_standard'),
	new weTagDataOption('is_fallback_to_standard'),
	new weTagDataOption('is_fallback_to_prefs'),
	new weTagDataOption('is_country_fallback_to_prefs')
	), false, '');
$this->TypeAttribute = new weTagData_typeAttribute('type', array(
	new weTagDataOption('-', false, '', array(), array()),
	new weTagDataOption('text', false, '', array($name, $hyperlink, $href, $target, $num_format, $alt, $max, $striphtml, $htmlspecialchars, $triggerid), array($name)),
	new weTagDataOption('date', false, '', array($name, $hyperlink, $href, $target, $format, $alt, $max, $htmlspecialchars, $triggerid), array($name)),
	new weTagDataOption('img', false, '', array($name, $hyperlink, $href, $target, $thumbnail, $src, $width, $height, $border, $hspace, $vspace, $align, $onlyImg, $triggerid), array($name)),
	new weTagDataOption('flashmovie', false, '', array($name, $width, $height, $triggerid), array($name)),
	new weTagDataOption('href', false, '', array($name), array($name)),
	new weTagDataOption('link', false, '', array($name), array($name)),
	new weTagDataOption('day', false, '', array(), array()),
	new weTagDataOption('dayname', false, '', array(), array()),
	new weTagDataOption('week', false, '', array(), array()),
	new weTagDataOption('month', false, '', array(), array()),
	new weTagDataOption('monthname', false, '', array(), array()),
	new weTagDataOption('year', false, '', array(), array()),
	new weTagDataOption('select', false, 'object', array($name, $usekey, $htmlspecialchars, $triggerid), array($name)),
	new weTagDataOption('binary', false, 'object', array($name, $hyperlink, $href, $target, $only), array($name)),
	new weTagDataOption('float', false, '', array($name, $hyperlink, $href, $target, $num_format, $triggerid), array($name)),
	new weTagDataOption('int', false, 'object', array($name, $hyperlink, $href, $target, $triggerid), array($name)),
	new weTagDataOption('collection', false, '', array($name), array($name)),
	new weTagDataOption('shopVat', false, '', array($vatfield), array()),
	new weTagDataOption('shopCategory', false, '', array($catfield, $showpath, $rootdir), array()),
	new weTagDataOption('checkbox', false, '', array($name), array($name)),
	new weTagDataOption('country', false, '', array($outputlanguage, $doc), array()),
	new weTagDataOption('language', false, '', array($outputlanguage, $doc), array())
	), false, '');

$this->Attributes = array($name, $classid, $hyperlink, $tid, $href, $target, $class, $style, $format, $num_format, $thumbnail, $id, $parentidname, $winprops, $alt, $max, $src,
	$width, $height, $border, $hspace, $vspace, $align, $only, $onlyImg, $htmlspecialchars, $seeMode, $xml, $win2iso, $listviewname, $striphtml, $outputlanguage, $doc, $triggerid,
	$usekey, $vatfield, $catfield, $showpath, $rootdir);

