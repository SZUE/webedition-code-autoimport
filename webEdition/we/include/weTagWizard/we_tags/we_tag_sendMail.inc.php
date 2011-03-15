<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id452_id'] = new weTagData_textAttribute('452', 'id', false, '');
$GLOBALS['weTagWizard']['attribute']['id453_subject'] = new weTagData_textAttribute('453', 'subject', false, '');
$GLOBALS['weTagWizard']['attribute']['id454_recipient'] = new weTagData_textAttribute('454', 'recipient', true, '');
$GLOBALS['weTagWizard']['attribute']['id825_recipientCC'] = new weTagData_textAttribute('(825', 'recipientCC', false, '');
$GLOBALS['weTagWizard']['attribute']['id826_recipientBCC'] = new weTagData_textAttribute('(826', 'recipientBCC', false, '');
$GLOBALS['weTagWizard']['attribute']['id455_from'] = new weTagData_textAttribute('455', 'from', true, '');
$GLOBALS['weTagWizard']['attribute']['id456_reply'] = new weTagData_textAttribute('456', 'reply', false, '');
$GLOBALS['weTagWizard']['attribute']['id457_mimetype'] = new weTagData_selectAttribute('457', 'mimetype', array(new weTagDataOption('text/plain', false, ''), new weTagDataOption('text/html', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id458_charset'] = new weTagData_textAttribute('458', 'charset', false, '');
$GLOBALS['weTagWizard']['attribute']['id814_includeimages'] = new weTagData_selectAttribute('814', 'includeimages', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id827_usebasehref'] = new weTagData_selectAttribute('827', 'usebasehref', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id828_useformmaillog'] = new weTagData_selectAttribute('828', 'useformmaillog', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id829_useformmailblock'] = new weTagData_selectAttribute('829', 'useformmailblock', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
