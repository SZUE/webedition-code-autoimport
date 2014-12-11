<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$options = array();
$opts = we_shop_category::getShopCatFieldsFromDir('Path');
foreach($opts as $k => $v){
	$options[] = new weTagDataOption($v, $k);
}
$this->Attributes[] = new weTagData_selectAttribute('id', $options, false);

$this->Attributes[] = new weTagData_selectAttribute('fromdoc', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(
	new weTagDataOption('self'),
	new weTagDataOption('top'),
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('field', array(
	new weTagDataOption('id'),
	new weTagDataOption('category'),
	new weTagDataOption('path'),
	new weTagDataOption('title'),
	new weTagDataOption('description'),
	new weTagDataOption('is_destinationprinciple'),
	new weTagDataOption('is_from doc_object'),
	new weTagDataOption('is_fallback_to_standard'),
	new weTagDataOption('is_fallback_to_active')
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('showpath', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_textAttribute('rootdir', false, '');