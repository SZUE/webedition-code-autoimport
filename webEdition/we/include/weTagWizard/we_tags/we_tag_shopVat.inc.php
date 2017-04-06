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
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

if(defined('WE_SHOP_VAT_TABLE')){
	if(we_shop_category::isCategoryMode()){
		$this->Attributes[] = new we_tagData_selectAttribute('field', [new we_tagData_option('id'),
			new we_tagData_option('vat'),
			new we_tagData_option('name'),
			new we_tagData_option('country'),
			new we_tagData_option('countrycode'),
			new we_tagData_option('is_standard'),
			new we_tagData_option('is_fallback_to_standard'),
			new we_tagData_option('is_fallback_to_prefs'),
			new we_tagData_option('is_country_fallback_to_prefs')
			], false, '');
	}

	$options = [];
	$vats = we_shop_vats::getAllShopVATs();
	foreach($vats as $vat){
		$options[] = new we_tagData_option($vat->vat . '% - ' . $vat->getNaturalizedText() . ' (' . $vat->territory . ')', $vat->id);
	}
	$this->Attributes[] = new we_tagData_selectAttribute('id', $options, false);

	if(we_shop_category::isCategoryMode()){
		$options = [];
		$shopcats = we_shop_category::getShopCatFieldsFromDir('Path', true);
		foreach($shopcats as $k => $v){
			$options[] = new we_tagData_option($v, $k);
		}
		$this->Attributes[] = new we_tagData_selectAttribute('shopcategoryid', $options, false);
		//TODO: this way of creating country select is too expensive (create we_html_select, then iterrate its options just to create it aganin...): make class weTagData_countryAttribute
		$options = [];
		foreach(we_html_tools::htmlSelectCountry('', '', 1, [], false, [], 50, 'defaultfont', true, true) as $v){
			$options[] = new we_tagData_option($v->content, isset($v->attribs['value']) ? $v->attribs['value'] : '', '', [], [], isset($v->attribs['disabled']) ? $v->attribs['disabled'] : '');
		}
		$this->Attributes[] = new we_tagData_selectAttribute('countrycode', $options, false, '');
		$this->Attributes[] = new we_tagData_textAttribute('customerid', false, '');
	}
}
