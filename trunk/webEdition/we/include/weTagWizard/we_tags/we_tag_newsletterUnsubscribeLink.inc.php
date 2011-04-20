<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectorAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

if(defined("FILE_TABLE")) {
$GLOBALS['weTagWizard']['attribute']['id382_id'] = new weTagData_selectorAttribute('382', 'id',FILE_TABLE, 'text/webedition', true, '');
$GLOBALS['weTagWizard']['attribute']['id4_plain'] = new weTagData_selectAttribute('4', 'plain', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');

}
