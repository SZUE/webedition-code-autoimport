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

$this->Attributes[] = new we_tagData_selectAttribute('field', [new we_tagData_option('id'),
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
$this->Attributes[] = new we_tagData_selectAttribute('match', $options, false);
$this->Attributes[] = new we_tagData_selectAttribute('ignorefallbacks', we_tagData_selectAttribute::getTrueFalse(), false, '');
