<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id621_id'] = new weTagData_textAttribute('621', 'id', false, '');
$GLOBALS['weTagWizard']['attribute']['id844_allowredirect'] = new weTagData_selectAttribute('844', 'allowredirect', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id866_writeto'] = new weTagData_selectAttribute('866', 'writeto', array(new weTagDataOption('voting', false, ''), new weTagDataOption('session', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id846_deletesessiondata'] = new weTagData_selectAttribute('846', 'deletesessiondata', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id845_additionalfields'] = new weTagData_textAttribute('845', 'additionalfields', false, '');
