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

$name = new we_tagData_textAttribute('name', true, '');
$match = new we_tagData_textAttribute('match', true, '');
$operator = new we_tagData_selectAttribute('operator', [new we_tagData_option('equal'),
	new we_tagData_option('less'),
	new we_tagData_option('less|equal'),
	new we_tagData_option('greater'),
	new we_tagData_option('greater|equal'),
	new we_tagData_option('contains'),
	new we_tagData_option('isin'),
	], false, '');
$striphtml = new we_tagData_selectAttribute('striphtml', [new we_tagData_option('false'),
	new we_tagData_option('true'),
	], false, '');
$usekey = new we_tagData_selectAttribute('usekey', we_tagData_selectAttribute::getTrueFalse(), false, '');

if(defined('SHOP_ORDER_TABLE')){
	$catfield = new we_tagData_selectAttribute('field', [new we_tagData_option('id'),
		new we_tagData_option('is_destinationprinciple'),
		new we_tagData_option('is_from doc_object'),
		new we_tagData_option('is_fallback_to_standard'),
		new we_tagData_option('is_fallback_to_active')
		], false, '');

	$options = [];
	$opts = we_shop_category::getShopCatFieldsFromDir('Path', true);
	foreach($opts as $k => $v){
		$options[] = new we_tagData_option($v, $k);
	}
	$catmatch = new we_tagData_selectAttribute('match', $options, false);
	$ignorefallbacks = new we_tagData_selectAttribute('ignorefallbacks', we_tagData_selectAttribute::getTrueFalse(), false, '');
	$vatfield = new we_tagData_selectAttribute('field', [new we_tagData_option('id'),
		new we_tagData_option('is_standard'),
		new we_tagData_option('is_fallback_to_standard'),
		new we_tagData_option('is_fallback_to_prefs'),
		new we_tagData_option('is_country_fallback_to_prefs')
		], false, '');

	$options = [];
	$vats = we_shop_vats::getAllShopVATs();
	foreach($vats as $vat){
		$options[] = new we_tagData_option($vat->vat . '% - ' . $vat->getNaturalizedText() . ' (' . $vat->territory . ')', $vat->id);
	}
	$vatmatch = new we_tagData_selectAttribute('match', $options, false);
	$shopVatAttributes = we_shop_category::isCategoryMode() ? [$vatfield, $vatmatch] : [$name, $match, $operator, $striphtml, $usekey];
}

$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('text', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new we_tagData_option('date', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new we_tagData_option('img', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new we_tagData_option('flashmovie', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new we_tagData_option('href', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new we_tagData_option('link', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new we_tagData_option('day', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new we_tagData_option('dayname', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new we_tagData_option('month', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new we_tagData_option('monthname', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new we_tagData_option('year', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new we_tagData_option('select', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new we_tagData_option('binary', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new we_tagData_option('float', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new we_tagData_option('int', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new we_tagData_option('collection', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	new we_tagData_option('shopCategory', false, 'shop', (defined('SHOP_ORDER_TABLE') ? [$catfield, $catmatch, $ignorefallbacks,] : []), []),
	new we_tagData_option('shopVat', false, 'shop', isset($shopVatAttributes) ? $shopVatAttributes : [], defined('SHOP_ORDER_TABLE') && !we_shop_category::isCategoryMode() ? [
		$name, $match] : []),
	new we_tagData_option('checkbox', false, '', [$name, $match, $operator, $striphtml, $usekey], [$name, $match]),
	], true, '');

$this->Attributes = [$name, $match, $operator, $striphtml, $usekey];

if(defined('SHOP_ORDER_TABLE')){
	$this->Attributes = array_merge($this->Attributes, [$catfield, $catmatch, $ignorefallbacks, $vatfield, $vatmatch]);
}