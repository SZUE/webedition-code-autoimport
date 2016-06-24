<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$options = [];
$opts = we_shop_category::getShopCatFieldsFromDir('Path', true);
foreach($opts as $k => $v){
	$options[] = new weTagDataOption($v, $k);
}
$this->Attributes = array(
	new weTagData_selectAttribute('id', $options, false),
	/* temorarily disabled
	  $this->Attributes[] = new weTagData_selectAttribute('doc', array(
	  new weTagDataOption('self'),
	  new weTagDataOption('top'),
	  ), false, '');
	 *
	 */
	new weTagData_selectAttribute('field', array(
		new weTagDataOption('id'),
		new weTagDataOption('category'),
		new weTagDataOption('path'),
		new weTagDataOption('title'),
		new weTagDataOption('description'),
		new weTagDataOption('is_destinationprinciple'),
		new weTagDataOption('is_from_doc_object'),
		new weTagDataOption('is_fallback_to_standard'),
		new weTagDataOption('is_fallback_to_active')
		), false, ''),
	new weTagData_selectAttribute('showpath', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_textAttribute('rootdir', false, ''),
);
