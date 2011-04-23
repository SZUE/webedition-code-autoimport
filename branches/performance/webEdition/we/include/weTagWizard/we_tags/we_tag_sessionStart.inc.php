<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_sqlColAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;
if(defined("CUSTOMER_TABLE")) {
$GLOBALS['weTagWizard']['attribute']['id873_persistentlogins'] = new weTagData_selectAttribute('873', 'persistentlogins', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id874_onlinemonitor'] = new weTagData_selectAttribute('874', 'onlinemonitor', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id875_monitorgroupfield'] = new weTagData_sqlColAttribute('875', 'monitorgroupfield', CUSTOMER_TABLE, true, array(), '');
$GLOBALS['weTagWizard']['attribute']['id583_monitordoc'] = new weTagData_selectAttribute('583', 'monitordoc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')), false, '');

}
