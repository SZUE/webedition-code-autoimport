<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectorAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = true;

$GLOBALS['weTagWizard']['attribute']['id255_path'] = new weTagData_textAttribute('255', 'path', false, '');
$GLOBALS['weTagWizard']['attribute']['id256_doc'] = new weTagData_selectAttribute('256', 'doc', array(new weTagDataOption('top', false, ''), new weTagDataOption('self', false, '')), false, '');
if(defined("FILE_TABLE")) { $GLOBALS['weTagWizard']['attribute']
['id817_id'] = new weTagData_selectorAttribute('817', 'id',FILE_TABLE, 'folder', false, ''); }
