<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$bannerName = new weTagData_choiceAttribute('name', array(
	new weTagDataOption('DefaultBanner'),
	), false, false, 'banner');
$newsletterName = new weTagData_choiceAttribute('name', array(
//	new weTagDataOption('DefaultBanner'),
	), false, false, 'newsletter');
$shopName = new weTagData_choiceAttribute('name', array(
	new weTagDataOption('vatRule'),
	new weTagDataOption('shippingControl'),
	new weTagDataOption('statusMails'),
	), false, false, 'shop');
$field = new weTagData_textAttribute('field', false, '');



$this->TypeAttribute = new weTagData_typeAttribute('type', array(
	new weTagDataOption('banner', false, 'banner', array($bannerName), array($bannerName)),
	new weTagDataOption('newsletter', false, 'newsletter', array($newsletterName), array($newsletterName)),
	new weTagDataOption('shop', false, 'shop', array($shopName, $field), array($shopName, $field)))
	, false, '');

$this->Attributes = array(
	$bannerName, $newsletterName, $shopName, $field
);
