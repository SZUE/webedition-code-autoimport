<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id618_shopname'] = new weTagData_textAttribute('618', 'shopname', true, '');
$GLOBALS['weTagWizard']['attribute']['id619_pricename'] = new weTagData_textAttribute('619', 'pricename', true, '');
$GLOBALS['weTagWizard']['attribute']['id620_netprices'] = new weTagData_selectAttribute('620', 'netprices', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id837_shipping'] = new weTagData_textAttribute('837', 'shipping', false, '');
$GLOBALS['weTagWizard']['attribute']['id838_shippingisnet'] = new weTagData_textAttribute('838', 'shippingisnet', false, '');
$GLOBALS['weTagWizard']['attribute']['id839_shippingvatrate'] = new weTagData_textAttribute('839', 'shippingvatrate', false, '');
