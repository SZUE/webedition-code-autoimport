<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$bannerName = new weTagData_choiceAttribute('name', [new weTagDataOption('DefaultBanner'),
	], false, false, 'banner');
$newsletterName = new weTagData_choiceAttribute('name', [
//	new weTagDataOption('DefaultBanner'),
	], false, false, 'newsletter');
$shopName = new weTagData_choiceAttribute('name', [new weTagDataOption('vatRule'),
	new weTagDataOption('shippingControl'),
	new weTagDataOption('statusMails'),
	], false, false, 'shop');
$field = new weTagData_textAttribute('field', false, '');



$this->TypeAttribute = new weTagData_typeAttribute('type', [new weTagDataOption('banner', false, 'banner', [$bannerName], [$bannerName]),
	new weTagDataOption('newsletter', false, 'newsletter', [$newsletterName], [$newsletterName]),
	new weTagDataOption('shop', false, 'shop', [$shopName, $field], [$shopName, $field])]
	, false, '');

$this->Attributes = [$bannerName, $newsletterName, $shopName, $field];
