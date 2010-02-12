<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id857_type'] = new weTagData_selectAttribute('857', 'type', array(new weTagDataOption('complete', false, ''), new weTagDataOption('language', false, ''), new weTagDataOption('country', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id858_case'] = new weTagData_selectAttribute('858', 'case', array(new weTagDataOption('unchanged', false, ''), new weTagDataOption('uppercase', false, ''), new weTagDataOption('lowercase', false, '')), false, '');

$GLOBALS['weTagWizard']['attribute']['id859_cachelifetime'] = new weTagData_textAttribute('859', 'cachelifetime', false, '');
?>