<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id92_shopname'] = new weTagData_textAttribute('92', 'shopname', true, '');
$GLOBALS['weTagWizard']['attribute']['id880_deleteshoponlogout'] = new weTagData_selectAttribute('880', 'deleteshoponlogout', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id881_deleteshop'] = new weTagData_selectAttribute('881', 'deleteshop', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
