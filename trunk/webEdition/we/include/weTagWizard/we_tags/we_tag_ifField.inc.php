<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = true;

$GLOBALS['weTagWizard']['attribute']['id690_name'] = new weTagData_textAttribute('690', 'name', true, '');
$GLOBALS['weTagWizard']['attribute']['id689_type'] = new weTagData_selectAttribute('689', 'type', array(new weTagDataOption('text', false, ''), new weTagDataOption('date', false, ''), new weTagDataOption('img', false, ''), new weTagDataOption('flashmovie', false, ''), new weTagDataOption('href', false, ''), new weTagDataOption('link', false, ''), new weTagDataOption('day', false, ''), new weTagDataOption('dayname', false, ''), new weTagDataOption('month', false, ''), new weTagDataOption('monthname', false, ''), new weTagDataOption('year', false, ''), new weTagDataOption('select', false, ''), new weTagDataOption('binary', false, ''), new weTagDataOption('float', false, ''), new weTagDataOption('int', false, ''), new weTagDataOption('shopVat', false, ''), new weTagDataOption('checkbox', false, '')), true, '');
$GLOBALS['weTagWizard']['attribute']['id691_match'] = new weTagData_textAttribute('691', 'match', false, '');
$GLOBALS['weTagWizard']['attribute']['id711_striphtml'] = new weTagData_selectAttribute('711', 'striphtml', array(new weTagDataOption('false', false, ''), new weTagDataOption('true', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id870_operator'] = new weTagData_selectAttribute('870', 'operator', array(new weTagDataOption('equal', false, ''), new weTagDataOption('less', false, ''), new weTagDataOption('less|equal', false, ''), new weTagDataOption('greater', false, ''), new weTagDataOption('greater|equal', false, ''), new weTagDataOption('contains', false, '')), false, '');
