<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_choiceAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = true;

$GLOBALS['weTagWizard']['attribute']['id588_name'] = new weTagData_selectAttribute('588', 'name', array(new weTagDataOption('question', false, ''), new weTagDataOption('answer', false, ''), new weTagDataOption('result', false, ''), new weTagDataOption('id', false, ''), new weTagDataOption('date', false, '')), true, '');
$GLOBALS['weTagWizard']['attribute']['id589_type'] = new weTagData_selectAttribute('589', 'type', array(new weTagDataOption('text', false, ''), new weTagDataOption('radio', false, ''), new weTagDataOption('checkbox', false, ''), new weTagDataOption('select', false, ''), new weTagDataOption('count', false, ''), new weTagDataOption('percent', false, ''), new weTagDataOption('total', false, ''), new weTagDataOption('answer', false, ''), new weTagDataOption('voting', false, ''),new weTagDataOption('textinput', false, ''),new weTagDataOption('textarea', false, ''), new weTagDataOption('image', false, ''),new weTagDataOption('media', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id691_match'] = new weTagData_textAttribute('691', 'match', false, '');
$GLOBALS['weTagWizard']['attribute']['id870_operator'] = new weTagData_selectAttribute('870', 'operator', array(new weTagDataOption('equal', false, ''), new weTagDataOption('less', false, ''), new weTagDataOption('less|equal', false, ''), new weTagDataOption('greater', false, ''), new weTagDataOption('greater|equal', false, ''), new weTagDataOption('contains', false, '')), false, '');
