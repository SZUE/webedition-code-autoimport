<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

if(defined('WE_SHOP_VAT_TABLE')){
	if(!we_shop_category::isCategoryMode()){
		$options = array();
		$vats = we_shop_vats::getAllShopVATs();
		foreach($vats as $vat){
			$options[] = new weTagDataOption($vat->vat . '% - ' . $vat->getNaturalizedText() . ' (' . $vat->territory  . ')', $vat->id);
		}
		$this->Attributes[] = new weTagData_selectAttribute('id', $options, false);
	} else {
		$this->Attributes[] = new weTagData_selectAttribute('field', array(
			new weTagDataOption('id'),
			new weTagDataOption('vat'),
			new weTagDataOption('name'),
			new weTagDataOption('country'),
			new weTagDataOption('country_iso'),
			new weTagDataOption('is_standard'),
			new weTagDataOption('is_fallback_to_standard'),
			new weTagDataOption('is_fallback_to_prefs'),
			new weTagDataOption('is_country_fallback_to_prefs')
		), false, '');
		$options = array();
		$shopcats = we_shop_category::getShopCatFieldsFromDir('Path', false, 0, true, true, true, '');
		foreach($shopcats as $k => $v){
			$options[] = new weTagDataOption($v, $k);
		}
		$this->Attributes[] = new weTagData_selectAttribute('shopcategoryid', $options, false);
		$this->Attributes[] = new weTagData_textAttribute('customerid', false, '');
		$this->Attributes[] = new weTagData_textAttribute('country', false, '');
	}
}
