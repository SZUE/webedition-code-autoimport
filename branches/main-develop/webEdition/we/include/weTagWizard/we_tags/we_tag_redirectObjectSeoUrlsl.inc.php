<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectorAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

if(defined("FILE_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id882_error404doc'] = new weTagData_selectorAttribute('882', 'error404doc',FILE_TABLE, 'text/webedition', true, ''); }

?>