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
$this->Groups[] = 'if_tags';
//$this->Module = 'object';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$name = new weTagData_textAttribute('name', true, '');
$match = new weTagData_textAttribute('match', true, '');
$operator = new weTagData_selectAttribute('operator', [new weTagDataOption('equal'),
	new weTagDataOption('less'),
	new weTagDataOption('less|equal'),
	new weTagDataOption('greater'),
	new weTagDataOption('greater|equal'),
	new weTagDataOption('contains'),
	new weTagDataOption('isin'),
	], false, '');
$striphtml = new weTagData_selectAttribute('striphtml', [new weTagDataOption('false'),
	new weTagDataOption('true'),
	], false, '');
$usekey = new weTagData_selectAttribute('usekey', weTagData_selectAttribute::getTrueFalse(), false, '');

if(defined('SHOP_ORDER_TABLE')){
	$catfield = new weTagData_selectAttribute('field', [new weTagDataOption('id'),
		new weTagDataOption('is_destinationprinciple'),
		new weTagDataOption('is_from doc_object'),
		new weTagDataOption('is_fallback_to_standard'),
		new weTagDataOption('is_fallback_to_active')
		], false, '');

	$options = [];
	$opts = we_shop_category::getShopCatFieldsFromDir('Path', true);
	foreach($opts as $k => $v){
		$options[] = new weTagDataOption($v, $k);
	}
	$catmatch = new weTagData_selectAttribute('match', $options, false);
	$ignorefallbacks = new weTagData_selectAttribute('ignorefallbacks', weTagData_selectAttribute::getTrueFalse(), false, '');
	$vatfield = new weTagData_selectAttribute('field', [new weTagDataOption('id'),
		new weTagDataOption('is_standard'),
		new weTagDataOption('is_fallback_to_standard'),
		new weTagDataOption('is_fallback_to_prefs'),
		new weTagDataOption('is_country_fallback_to_prefs')
		], false, '');

	$options = [];
	$vats = we_shop_vats::getAllShopVATs();
	foreach($vats as $vat){
		$options[] = new weTagDataOption($vat->vat . '% - ' . $vat->getNaturalizedText() . ' (' . $vat->territory . ')', $vat->id);
	}
	$vatmatch = new weTagData_selectAttribute('match', $options, false);
	$shopVatAttributes = we_shop_category::isCategoryMode() ? [$vatfield, $vatmatch] : [$name, $match, $operator, $striphtml, $usekey];
}

$this->TypeAttribute = new weTagData_typeAttribute('type', [new weTagDataOption('text', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new weTagDataOption('date', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new weTagDataOption('img', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new weTagDataOption('flashmovie', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new weTagDataOption('href', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new weTagDataOption('link', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new weTagDataOption('day', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new weTagDataOption('dayname', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new weTagDataOption('month', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new weTagDataOption('monthname', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new weTagDataOption('year', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new weTagDataOption('select', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new weTagDataOption('binary', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new weTagDataOption('float', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new weTagDataOption('int', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new weTagDataOption('collection', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new weTagDataOption('shopCategory', false, 'shop', (defined('SHOP_ORDER_TABLE') ? [$catfield, $catmatch, $ignorefallbacks,] : []), []),
	new weTagDataOption('shopVat', false, 'shop', isset($shopVatAttributes) ? $shopVatAttributes : [], defined('SHOP_ORDER_TABLE') && !we_shop_category::isCategoryMode() ? [
		$name, $match] : []),
	new weTagDataOption('checkbox', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	], true, '');

$this->Attributes = [$name, $match, $operator, $striphtml, $usekey];

if(defined('SHOP_ORDER_TABLE')){
	$this->Attributes = array_merge($this->Attributes, [$catfield, $catmatch, $ignorefallbacks, $vatfield, $vatmatch]);
}