<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id24_shopname'] = new weTagData_textAttribute('24', 'shopname', true, '');
$GLOBALS['weTagWizard']['attribute']['id25_floatquantities'] = new weTagData_selectAttribute('25', 'floatquantities', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
