<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectorAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_sqlRowAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id145_name'] = new weTagData_textAttribute('145', 'name', true, '');
$GLOBALS['weTagWizard']['attribute']['id146_width'] = new weTagData_textAttribute('146', 'width', false, '');
$GLOBALS['weTagWizard']['attribute']['id147_height'] = new weTagData_textAttribute('147', 'height', false, '');
$GLOBALS['weTagWizard']['attribute']['id824_wmode'] = new weTagData_selectAttribute('824', 'wmode', array(new weTagDataOption('window', false, ''), new weTagDataOption('opaque', false, ''),new weTagDataOption('transparent', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id148_alt'] = new weTagData_textAttribute('148', 'alt', false, '');
if(defined("FILE_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id820_startid'] = new weTagData_selectorAttribute('820', 'startid',FILE_TABLE, 'folder', false, ''); }
if(defined("FILE_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id821_parentid'] = new weTagData_selectorAttribute('821', 'parentid',FILE_TABLE, 'folder', false, ''); }
$GLOBALS['weTagWizard']['attribute']['id633_showcontrol'] = new weTagData_textAttribute('633', 'showcontrol', false, '');
$GLOBALS['weTagWizard']['attribute']['id150_showflash'] = new weTagData_selectAttribute('150', 'showflash', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id628_xml'] = new weTagData_selectAttribute('628', 'xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id848_sizingrel'] = new weTagData_textAttribute('848', 'sizingrel', false, '');
$GLOBALS['weTagWizard']['attribute']['id860_sizingstyle'] = new weTagData_selectAttribute('860', 'sizingstyle', array(new weTagDataOption('none', false, ''), new weTagDataOption('em', false, ''), new weTagDataOption('ex', false, ''), new weTagDataOption('%', false, ''), new weTagDataOption('px', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id861_sizingbase'] = new weTagData_textAttribute('861', 'sizingbase', false, '');
$GLOBALS['weTagWizard']['attribute']['id478_to'] = new weTagData_selectAttribute('478', 'to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id479_nameto'] = new weTagData_textAttribute('479', 'nameto', false, '');

$GLOBALS['weTagWizard']['attribute']['id734_cachelifetime'] = new weTagData_textAttribute('734', 'cachelifetime', false, '');
