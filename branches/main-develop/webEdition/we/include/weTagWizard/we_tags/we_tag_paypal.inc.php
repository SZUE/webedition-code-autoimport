<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_choiceAttribute.class.php');
$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id405_shopname'] = new weTagData_textAttribute('405', 'shopname', true, '');
$GLOBALS['weTagWizard']['attribute']['id406_pricename'] = new weTagData_textAttribute('406', 'pricename', true, '');
$GLOBALS['weTagWizard']['attribute']['id407_usevat'] = new weTagData_selectAttribute('407', 'usevat', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id408_netprices'] = new weTagData_selectAttribute('408', 'netprices', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id835_countrycode'] = new weTagData_textAttribute('835', 'countrycode', false, '');
$GLOBALS['weTagWizard']['attribute']['id836_languagecode'] = new weTagData_textAttribute('836', 'languagecode', false, '');
$GLOBALS['weTagWizard']['attribute']['id837_shipping'] = new weTagData_textAttribute('837', 'shipping', false, '');
$GLOBALS['weTagWizard']['attribute']['id838_shippingisnet'] = new weTagData_textAttribute('838', 'shippingisnet', false, '');
$GLOBALS['weTagWizard']['attribute']['id839_shippingvatrate'] = new weTagData_textAttribute('839', 'shippingvatrate', false, '');
$GLOBALS['weTagWizard']['attribute']['id840_formtagonly'] = new weTagData_selectAttribute('840', 'formtagonly', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id841_messageredirectAuto'] = new weTagData_textAttribute('841', 'messageredirectAuto', false, '');
$GLOBALS['weTagWizard']['attribute']['id842_messageredirectMan'] = new weTagData_textAttribute('842', 'messageredirectMan', false, '');
$GLOBALS['weTagWizard']['attribute']['id865_charset'] = new weTagData_choiceAttribute('865', 'charset', array(new weTagDataOption('ISO-8859-1', false, ''), new weTagDataOption('ISO-8859-2', false, ''), new weTagDataOption('ISO-8859-3', false, ''), new weTagDataOption('ISO-8859-4', false, ''), new weTagDataOption('ISO-8859-5', false, ''), new weTagDataOption('ISO-8859-6', false, ''), new weTagDataOption('ISO-8859-7', false, ''), new weTagDataOption('ISO-8859-8', false, ''), new weTagDataOption('ISO-8859-9', false, ''), new weTagDataOption('ISO-8859-10', false, ''), new weTagDataOption('ISO-8859-11', false, ''), new weTagDataOption('ISO-8859-13', false, ''), new weTagDataOption('ISO-8859-14', false, ''), new weTagDataOption('ISO-8859-15', false, ''), new weTagDataOption('UTF-8', false, ''), new weTagDataOption('Windows-1251', false, ''), new weTagDataOption('Windows-1252', false, '')), false,true, '');
