<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_choiceAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = true;

$GLOBALS['weTagWizard']['attribute']['id258_type'] = new weTagData_choiceAttribute('258', 'type', array(new weTagDataOption('block', false, ''), new weTagDataOption('linklist', false, ''), new weTagDataOption('listdir', false, ''), new weTagDataOption('listview', false, '')), true,false, '');
$GLOBALS['weTagWizard']['attribute']['id259_position'] = new weTagData_choiceAttribute('259', 'position', array(new weTagDataOption('first', false, ''), new weTagDataOption('last', false, ''), new weTagDataOption('odd', false, ''), new weTagDataOption('even', false, '')), true,true, '');
$GLOBALS['weTagWizard']['attribute']['id260_reference'] = new weTagData_textAttribute('260', 'reference', false, '');
$GLOBALS['weTagWizard']['attribute']['id870_operator'] = new weTagData_selectAttribute('870', 'operator', array(new weTagDataOption('equal', false, ''), new weTagDataOption('less', false, ''), new weTagDataOption('less|equal', false, ''), new weTagDataOption('greater', false, ''), new weTagDataOption('greater|equal', false, '')), false, '');
