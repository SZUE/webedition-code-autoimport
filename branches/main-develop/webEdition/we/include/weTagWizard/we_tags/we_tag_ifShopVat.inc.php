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
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

if(defined('WE_SHOP_VAT_TABLE')){
	$options = [];
	$options[] = new we_tagData_option('', 0);
	$vats = we_shop_vats::getAllShopVATs();
	foreach($vats as $vat){
		$options[] = new we_tagData_option($vat->vat . '% - ' . $vat->getNaturalizedText() . ' (' . $vat->territory . ')', $vat->id);
	}

	if(!we_shop_category::isCategoryMode()){
		$this->Attributes[] = new we_tagData_selectAttribute('id', $options, true);
	} else {
		$this->Attributes[] = new we_tagData_selectAttribute('field', [new we_tagData_option('id'),
			new we_tagData_option('is_standard'),
			new we_tagData_option('is_fallback_to_standard'),
			new we_tagData_option('is_fallback_to_prefs'),
			new we_tagData_option('is_country_fallback_to_prefs')
			], false, '');
		$this->Attributes[] = new we_tagData_selectAttribute('match', $options, false);
	}
}
