<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_choiceAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id588_name'] = new weTagData_selectAttribute('588', 'name', array(new weTagDataOption('question', false, ''), new weTagDataOption('answer', false, ''), new weTagDataOption('result', false, ''), new weTagDataOption('id', false, ''), new weTagDataOption('date', false, '')), true, '');
$GLOBALS['weTagWizard']['attribute']['id589_type'] = new weTagData_selectAttribute('589', 'type', array(new weTagDataOption('text', false, ''), new weTagDataOption('radio', false, ''), new weTagDataOption('checkbox', false, ''), new weTagDataOption('select', false, ''), new weTagDataOption('count', false, ''), new weTagDataOption('percent', false, ''), new weTagDataOption('total', false, ''), new weTagDataOption('answer', false, ''), new weTagDataOption('voting', false, ''),new weTagDataOption('textinput', false, ''),new weTagDataOption('textarea', false, ''), new weTagDataOption('image', false, ''),new weTagDataOption('media', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id590_xml'] = new weTagData_selectAttribute('590', 'xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id591_format'] = new weTagData_textAttribute('591', 'format', false, '');
$GLOBALS['weTagWizard']['attribute']['id592_num_format'] = new weTagData_choiceAttribute('592', 'num_format', array(new weTagDataOption('german', false, ''), new weTagDataOption('french', false, ''), new weTagDataOption('english', false, ''), new weTagDataOption('swiss', false, '')), false,false, '');
$GLOBALS['weTagWizard']['attribute']['id593_precision'] = new weTagData_textAttribute('593', 'precision', false, '');
$GLOBALS['weTagWizard']['attribute']['id478_outputto'] = new weTagData_selectAttribute('478', 'to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id479_nameto'] = new weTagData_textAttribute('479', 'nameto', false, '');
?>