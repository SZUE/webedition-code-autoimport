<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = true;

$GLOBALS['weTagWizard']['attribute']['id720_htmlspecialchars'] = new weTagData_selectAttribute('720', 'htmlspecialchars', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id721_prefix'] = new weTagData_textAttribute('721', 'prefix', false, '');
$GLOBALS['weTagWizard']['attribute']['id722_suffix'] = new weTagData_textAttribute('722', 'suffix', false, '');
$GLOBALS['weTagWizard']['attribute']['id723_delimiter'] = new weTagData_textAttribute('723', 'delimiter', false, '');

$GLOBALS['weTagWizard']['attribute']['id734_cachelifetime'] = new weTagData_textAttribute('734', 'cachelifetime', false, '');
