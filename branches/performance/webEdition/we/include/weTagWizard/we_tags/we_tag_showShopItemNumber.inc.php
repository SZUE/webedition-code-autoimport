<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_choiceAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id501_shopname'] = new weTagData_textAttribute('501', 'shopname', true, '');
$GLOBALS['weTagWizard']['attribute']['id502_type'] = new weTagData_choiceAttribute('502', 'type', array(new weTagDataOption('select', false, ''), new weTagDataOption('textinput', false, ''), new weTagDataOption('print', false, '')), false,false, '');
$GLOBALS['weTagWizard']['attribute']['id503_start'] = new weTagData_textAttribute('503', 'start', false, '');
$GLOBALS['weTagWizard']['attribute']['id504_stop'] = new weTagData_textAttribute('504', 'stop', false, '');
$GLOBALS['weTagWizard']['attribute']['id25_floatquantities'] = new weTagData_selectAttribute('25', 'floatquantities', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id27_step'] = new weTagData_textAttribute('27', 'step', false, '1');
$GLOBALS['weTagWizard']['attribute']['id26_num_format'] = new weTagData_choiceAttribute('26', 'num_format', array(new weTagDataOption('german', false, ''), new weTagDataOption('french', false, ''), new weTagDataOption('english', false, ''), new weTagDataOption('swiss', false, '')), false,false, '');
$GLOBALS['weTagWizard']['attribute']['id478_to'] = new weTagData_selectAttribute('478', 'to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id479_nameto'] = new weTagData_textAttribute('479', 'nameto', false, '');
