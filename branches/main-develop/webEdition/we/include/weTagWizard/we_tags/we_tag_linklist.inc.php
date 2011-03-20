<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = true;

$GLOBALS['weTagWizard']['attribute']['id339_name'] = new weTagData_textAttribute('339', 'name', true, '');
$GLOBALS['weTagWizard']['attribute']['id649_limit'] = new weTagData_textAttribute('649', 'limit', false, '');
$GLOBALS['weTagWizard']['attribute']['id884_hidedirindex'] = new weTagData_selectAttribute('884', 'hidedirindex', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id885_objectseourls'] = new weTagData_selectAttribute('885', 'objectseourls', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
