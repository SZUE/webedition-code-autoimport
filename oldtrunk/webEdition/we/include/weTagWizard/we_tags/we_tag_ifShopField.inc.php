<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_choiceAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = true;

$GLOBALS['weTagWizard']['attribute']['id491_name'] = new weTagData_textAttribute('491', 'name', true, '');
$GLOBALS['weTagWizard']['attribute']['id492_reference'] = new weTagData_selectAttribute('492', 'reference', array(new weTagDataOption('article', false, ''), new weTagDataOption('cart', false, '')), true, '');
$GLOBALS['weTagWizard']['attribute']['id493_shopname'] = new weTagData_textAttribute('493', 'shopname', true, '');
$GLOBALS['weTagWizard']['attribute']['id691_match'] = new weTagData_textAttribute('691', 'match', true, '');
$GLOBALS['weTagWizard']['attribute']['id870_operator'] = new weTagData_selectAttribute('870', 'operator', array(new weTagDataOption('equal', false, ''), new weTagDataOption('less', false, ''), new weTagDataOption('less|equal', false, ''), new weTagDataOption('greater', false, ''), new weTagDataOption('greater|equal', false, ''), new weTagDataOption('contains', false, '')), false, '');
