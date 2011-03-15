<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectorAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_sqlRowAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id419_name'] = new weTagData_textAttribute('419', 'name', true, '');
$GLOBALS['weTagWizard']['attribute']['id420_width'] = new weTagData_textAttribute('420', 'width', false, '');
$GLOBALS['weTagWizard']['attribute']['id421_height'] = new weTagData_textAttribute('421', 'height', false, '');
if(defined("FILE_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id822_startid'] = new weTagData_selectorAttribute('822', 'startid',FILE_TABLE, 'folder', false, ''); }
if(defined("FILE_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id823_parentid'] = new weTagData_selectorAttribute('823', 'parentid',FILE_TABLE, 'folder', false, ''); }
$GLOBALS['weTagWizard']['attribute']['id422_showcontrol'] = new weTagData_selectAttribute('422', 'showcontrol', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id423_showquicktime'] = new weTagData_selectAttribute('423', 'showquicktime', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id628_xml'] = new weTagData_selectAttribute('628', 'xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id734_cachelifetime'] = new weTagData_textAttribute('734', 'cachelifetime', false, '');
$GLOBALS['weTagWizard']['attribute']['id849_sizingrel'] = new weTagData_textAttribute('849', 'sizingrel', false, '');
$GLOBALS['weTagWizard']['attribute']['id860_sizingstyle'] = new weTagData_selectAttribute('860', 'sizingstyle', array(new weTagDataOption('none', false, ''), new weTagDataOption('em', false, ''), new weTagDataOption('ex', false, ''), new weTagDataOption('%', false, ''), new weTagDataOption('px', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id861_sizingbase'] = new weTagData_textAttribute('861', 'sizingbase', false, '');
