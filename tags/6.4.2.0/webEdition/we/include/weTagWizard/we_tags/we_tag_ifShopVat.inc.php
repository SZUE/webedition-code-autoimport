<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

if(defined('WE_SHOP_VAT_TABLE')){
	$options = array();
	$options[] = new weTagDataOption('', 0);
	$vats = we_shop_vats::getAllShopVATs();
	foreach($vats as $vat){
		$options[] = new weTagDataOption($vat->vat . '% - ' . $vat->getNaturalizedText() . ' (' . $vat->territory  . ')', $vat->id);
	}

	if(!we_shop_category::isCategoryMode()){
		$this->Attributes[] = new weTagData_selectAttribute('id', $options, true);
	} else {
		$this->Attributes[] = new weTagData_selectAttribute('field', array(
			new weTagDataOption('id'),
			new weTagDataOption('is_standard'),
			new weTagDataOption('is_fallback_to_standard'),
			new weTagDataOption('is_fallback_to_prefs'),
			new weTagDataOption('is_country_fallback_to_prefs')
		), false, '');
		$this->Attributes[] = new weTagData_selectAttribute('match', $options, false);
	}

}
