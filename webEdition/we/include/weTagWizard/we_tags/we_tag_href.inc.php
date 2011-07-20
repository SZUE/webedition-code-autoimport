<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id184_name'] = new weTagData_textAttribute('184', 'name', true, '');
$GLOBALS['weTagWizard']['attribute']['id185_size'] = new weTagData_textAttribute('185', 'size', false, '');
$GLOBALS['weTagWizard']['attribute']['id186_type'] = new weTagData_selectAttribute('186', 'type', array(new weTagDataOption('all', false, ''), new weTagDataOption('int', false, ''), new weTagDataOption('ext', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id187_include'] = new weTagData_selectAttribute('187', 'include', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id188_file'] = new weTagData_selectAttribute('188', 'file', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id189_directory'] = new weTagData_selectAttribute('189', 'directory', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id190_reload'] = new weTagData_selectAttribute('190', 'reload', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id634_user'] = new weTagData_textAttribute('634', 'user', false, 'users');
$GLOBALS['weTagWizard']['attribute']['id650_rootdir'] = new weTagData_textAttribute('650', 'rootdir', false, '');
$GLOBALS['weTagWizard']['attribute']['id478_to'] = new weTagData_selectAttribute('478', 'to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id479_nameto'] = new weTagData_textAttribute('479', 'nameto', false, '');

$GLOBALS['weTagWizard']['attribute']['id734_cachelifetime'] = new weTagData_textAttribute('734', 'cachelifetime', false, '');
