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

$this->Attributes[] = new weTagData_selectAttribute('field', array(
	new weTagDataOption('id'),
	new weTagDataOption('is_destinationprinciple'),
	new weTagDataOption('is_from doc_object'),
	new weTagDataOption('is_fallback_to_standard'),
	new weTagDataOption('is_fallback_to_active')
), false, '');
$options = array();
$opts = we_shop_category::getShopCatFieldsFromDir('Path', true);
foreach($opts as $k => $v){
	$options[] = new weTagDataOption($v, $k);
}
$this->Attributes[] = new weTagData_selectAttribute('match', $options, false);
$this->Attributes[] = new weTagData_selectAttribute('ignorefallbacks', weTagData_selectAttribute::getTrueFalse(), false, '');
