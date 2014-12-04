<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

if(defined('WE_SHOP_VAT_TABLE')){
	$options = array();
	$vats = we_shop_vats::getAllShopVATs();
	foreach($vats as $vat){
		$options[] = new weTagDataOption($vat->vat . '% - ' . $vat->getNaturalizedText() . ' (' . $vat->territory  . ')', $vat->id);
	}
	$this->Attributes[] = new weTagData_selectAttribute('id', $options, true);
}
