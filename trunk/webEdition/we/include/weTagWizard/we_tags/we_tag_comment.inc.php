<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_choiceAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = true;

$GLOBALS['weTagWizard']['attribute']['id54_type'] = new weTagData_choiceAttribute('54', 'type', array(new weTagDataOption('xml', false, ''), new weTagDataOption('html', false, ''), new weTagDataOption('js', false, '')), false,false, '');
