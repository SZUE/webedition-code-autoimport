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

$options = [];
$opts = we_shop_category::getShopCatFieldsFromDir('Path', true);
foreach($opts as $k => $v){
	$options[] = new we_tagData_option($v, $k);
}
$this->Attributes = [new we_tagData_selectAttribute('id', $options, false),
	/* temorarily disabled
	  $this->Attributes[] = new weTagData_selectAttribute('doc', [
	  new weTagDataOption('self'),
	  new weTagDataOption('top'),
	  ], false, '');
	 *
	 */
	new we_tagData_selectAttribute('field', [new we_tagData_option('id'),
		new we_tagData_option('category'),
		new we_tagData_option('path'),
		new we_tagData_option('title'),
		new we_tagData_option('description'),
		new we_tagData_option('is_destinationprinciple'),
		new we_tagData_option('is_from_doc_object'),
		new we_tagData_option('is_fallback_to_standard'),
		new we_tagData_option('is_fallback_to_active')
		], false, ''),
	new we_tagData_selectAttribute('showpath', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_textAttribute('rootdir', false, ''),
];
