<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id857_type'] = new weTagData_selectAttribute('857', 'type', array(new weTagDataOption('complete', false, ''), new weTagDataOption('language', false, ''), new weTagDataOption('country', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id858_case'] = new weTagData_selectAttribute('858', 'case', array(new weTagDataOption('unchanged', false, ''), new weTagDataOption('uppercase', false, ''), new weTagDataOption('lowercase', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id478_to'] = new weTagData_selectAttribute('478', 'to', array(new weTagDataOption('screen', false, ''), new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''),new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id479_nameto'] = new weTagData_textAttribute('479', 'nameto', false, '');

$GLOBALS['weTagWizard']['attribute']['id859_cachelifetime'] = new weTagData_textAttribute('859', 'cachelifetime', false, '');
