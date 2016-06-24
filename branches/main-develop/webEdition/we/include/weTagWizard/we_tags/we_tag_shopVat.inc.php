<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

if(defined('WE_SHOP_VAT_TABLE')){
	if(we_shop_category::isCategoryMode()){
		$this->Attributes[] = new weTagData_selectAttribute('field', array(
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
		
	}

	$options = [];
	$vats = we_shop_vats::getAllShopVATs();
	foreach($vats as $vat){
		$options[] = new weTagDataOption($vat->vat . '% - ' . $vat->getNaturalizedText() . ' (' . $vat->territory  . ')', $vat->id);
	}
	$this->Attributes[] = new weTagData_selectAttribute('id', $options, false);

	if(we_shop_category::isCategoryMode()){
		$options = [];
		$shopcats = we_shop_category::getShopCatFieldsFromDir('Path', true);
		foreach($shopcats as $k => $v){
			$options[] = new weTagDataOption($v, $k);
		}
		$this->Attributes[] = new weTagData_selectAttribute('shopcategoryid', $options, false);
		//TODO: this way of creating country select is too expensive (create we_html_select, then iterrate its options just to create it aganin...): make class weTagData_countryAttribute
		$options = [];
		foreach(we_html_tools::htmlSelectCountry('', '', 1, [], false, [], 50, 'defaultfont', true, true) as $v){
			$options[] = new weTagDataOption($v->content, isset($v->attribs['value']) ? $v->attribs['value'] : '', '', [], [], isset($v->attribs['disabled']) ? $v->attribs['disabled'] : '');
		}
		$this->Attributes[] = new weTagData_selectAttribute('countrycode', $options, false, '');
		$this->Attributes[] = new weTagData_textAttribute('customerid', false, '');
	}
}
