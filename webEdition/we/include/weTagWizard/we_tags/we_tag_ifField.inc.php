<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = 'object';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$name = new weTagData_textAttribute('name', true, '');
$match = new weTagData_textAttribute('match', true, '');
$operator = new weTagData_selectAttribute('operator', array(new weTagDataOption('equal'),
	new weTagDataOption('less'),
	new weTagDataOption('less|equal'),
	new weTagDataOption('greater'),
	new weTagDataOption('greater|equal'),
	new weTagDataOption('contains'),
	new weTagDataOption('isin'),
	), false, '');
$striphtml = new weTagData_selectAttribute('striphtml', array(new weTagDataOption('false'),
	new weTagDataOption('true'),
	), false, '');
$usekey = new weTagData_selectAttribute('usekey', weTagData_selectAttribute::getTrueFalse(), false, '');

if(defined('SHOP_TABLE')){
	$catfield = new weTagData_selectAttribute('field', array(
		new weTagDataOption('id'),
		new weTagDataOption('is_destinationprinciple'),
		new weTagDataOption('is_from doc_object'),
		new weTagDataOption('is_fallback_to_standard'),
		new weTagDataOption('is_fallback_to_active')
		), false, '');

	$options = [];
	$opts = we_shop_category::getShopCatFieldsFromDir('Path', true);
	foreach($opts as $k => $v){
		$options[] = new weTagDataOption($v, $k);
	}
	$catmatch = new weTagData_selectAttribute('match', $options, false);
	$ignorefallbacks = new weTagData_selectAttribute('ignorefallbacks', weTagData_selectAttribute::getTrueFalse(), false, '');
	$vatfield = new weTagData_selectAttribute('field', array(
		new weTagDataOption('id'),
		new weTagDataOption('is_standard'),
		new weTagDataOption('is_fallback_to_standard'),
		new weTagDataOption('is_fallback_to_prefs'),
		new weTagDataOption('is_country_fallback_to_prefs')
		), false, '');

	$options = [];
	$vats = we_shop_vats::getAllShopVATs();
	foreach($vats as $vat){
		$options[] = new weTagDataOption($vat->vat . '% - ' . $vat->getNaturalizedText() . ' (' . $vat->territory . ')', $vat->id);
	}
	$vatmatch = new weTagData_selectAttribute('match', $options, false);
	$shopVatAttributes = we_shop_category::isCategoryMode() ? array($vatfield, $vatmatch) : array($name, $match, $operator, $striphtml, $usekey);
}

$this->TypeAttribute = new weTagData_typeAttribute('type', array(
	new weTagDataOption('text', false, '', array($name, $match, $operator, $striphtml, $usekey), array($name, $match)),
	new weTagDataOption('date', false, '', array($name, $match, $operator, $striphtml, $usekey), array($name, $match)),
	new weTagDataOption('img', false, '', array($name, $match, $operator, $striphtml, $usekey), array($name, $match)),
	new weTagDataOption('flashmovie', false, '', array($name, $match, $operator, $striphtml, $usekey), array($name, $match)),
	new weTagDataOption('href', false, '', array($name, $match, $operator, $striphtml, $usekey), array($name, $match)),
	new weTagDataOption('link', false, '', array($name, $match, $operator, $striphtml, $usekey), array($name, $match)),
	new weTagDataOption('day', false, '', array($name, $match, $operator, $striphtml, $usekey), array($name, $match)),
	new weTagDataOption('dayname', false, '', array($name, $match, $operator, $striphtml, $usekey), array($name, $match)),
	new weTagDataOption('month', false, '', array($name, $match, $operator, $striphtml, $usekey), array($name, $match)),
	new weTagDataOption('monthname', false, '', array($name, $match, $operator, $striphtml, $usekey), array($name, $match)),
	new weTagDataOption('year', false, '', array($name, $match, $operator, $striphtml, $usekey), array($name, $match)),
	new weTagDataOption('select', false, '', array($name, $match, $operator, $striphtml, $usekey), array($name, $match)),
	new weTagDataOption('binary', false, '', array($name, $match, $operator, $striphtml, $usekey), array($name, $match)),
	new weTagDataOption('float', false, '', array($name, $match, $operator, $striphtml, $usekey), array($name, $match)),
	new weTagDataOption('int', false, '', array($name, $match, $operator, $striphtml, $usekey), array($name, $match)),
	new weTagDataOption('collection', false, '', array($name, $match, $operator, $striphtml, $usekey), array($name, $match)),
	new weTagDataOption('shopCategory', false, 'shop', (defined('SHOP_TABLE') ? array($catfield, $catmatch, $ignorefallbacks,) : []), []),
	new weTagDataOption('shopVat', false, 'shop', isset($shopVatAttributes) ? $shopVatAttributes : [], defined('SHOP_TABLE') && !we_shop_category::isCategoryMode() ? array($name, $match) : []),
	new weTagDataOption('checkbox', false, '', array($name, $match, $operator, $striphtml, $usekey), array($name, $match)),
	), true, '');

$this->Attributes = array($name, $match, $operator, $striphtml, $usekey);

if(defined('SHOP_TABLE')){
	$this->Attributes = array_merge($this->Attributes, array($catfield, $catmatch, $ignorefallbacks, $vatfield, $vatmatch));
}