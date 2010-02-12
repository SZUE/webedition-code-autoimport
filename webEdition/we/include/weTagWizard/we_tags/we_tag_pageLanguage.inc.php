<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id853_type'] = new weTagData_selectAttribute('853', 'type', array(new weTagDataOption('complete', false, ''), new weTagDataOption('language', false, ''), new weTagDataOption('country', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id854_case'] = new weTagData_selectAttribute('854', 'case', array(new weTagDataOption('unchanged', false, ''), new weTagDataOption('uppercase', false, ''), new weTagDataOption('lowercase', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id855_doc'] = new weTagData_selectAttribute('855', 'doc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')), false, '');

$GLOBALS['weTagWizard']['attribute']['id856_cachelifetime'] = new weTagData_textAttribute('856', 'cachelifetime', false, '');
?>