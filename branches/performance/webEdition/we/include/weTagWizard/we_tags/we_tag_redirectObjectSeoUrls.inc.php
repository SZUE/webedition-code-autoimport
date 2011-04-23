<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectorAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id883_hiddendirindex'] = new weTagData_selectAttribute('883', 'hiddendirindex', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
if(defined("FILE_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id882_error404doc'] = new weTagData_selectorAttribute('882', 'error404doc',FILE_TABLE, 'text/webedition', false, ''); }
$GLOBALS['weTagWizard']['attribute']['id886_suppresserrorcode'] = new weTagData_selectAttribute('886', 'suppresserrorcode', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
