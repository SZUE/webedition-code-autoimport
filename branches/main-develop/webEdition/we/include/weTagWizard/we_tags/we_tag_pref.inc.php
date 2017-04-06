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
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$bannerName = new we_tagData_choiceAttribute('name', [new we_tagData_option('DefaultBanner'),
	], false, false, 'banner');
$newsletterName = new we_tagData_choiceAttribute('name', [
//	new weTagDataOption('DefaultBanner'),
	], false, false, 'newsletter');
$shopName = new we_tagData_choiceAttribute('name', [new we_tagData_option('vatRule'),
	new we_tagData_option('shippingControl'),
	new we_tagData_option('statusMails'),
	], false, false, 'shop');
$field = new we_tagData_textAttribute('field', false, '');



$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('banner', false, 'banner', [$bannerName], [$bannerName]),
	new we_tagData_option('newsletter', false, 'newsletter', [$newsletterName], [$newsletterName]),
	new we_tagData_option('shop', false, 'shop', [$shopName, $field], [$shopName, $field])]
	, false, '');

$this->Attributes = [$bannerName, $newsletterName, $shopName, $field];
