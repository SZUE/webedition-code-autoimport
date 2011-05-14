<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_typeAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectorAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id886_type'] = new weTagData_typeAttribute('886', 'type', array(new weTagDataOption('document', false, '', array('id886_type','id553_id','id734_cachelifetime','id884_hidedirindex'), array('id553_id')), new weTagDataOption('object', false, 'object', array('id886_type','id887_id','id888_triggerid','id734_cachelifetime','id884_hidedirindex','id885_objectseourls'), array('id887_id'))), false, '');
if(defined("FILE_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id553_id'] = new weTagData_selectorAttribute('553', 'id',FILE_TABLE, 'text/webedition,image/*,text/css,text/js,application/*', true, ''); }
if(defined("OBJECT_FILES_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id887_id'] = new weTagData_selectorAttribute('887', 'id',OBJECT_FILES_TABLE, 'objectFile', true, ''); }
if(defined("FILE_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id888_triggerid'] = new weTagData_selectorAttribute('888', 'triggerid',FILE_TABLE, 'text/webedition', false, ''); }

$GLOBALS['weTagWizard']['attribute']['id734_cachelifetime'] = new weTagData_textAttribute('734', 'cachelifetime', false, '');
$GLOBALS['weTagWizard']['attribute']['id884_hidedirindex'] = new weTagData_selectAttribute('884', 'hidedirindex', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id885_objectseourls'] = new weTagData_selectAttribute('885', 'objectseourls', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');

