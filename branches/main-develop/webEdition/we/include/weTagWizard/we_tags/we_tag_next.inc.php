<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectorAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = true;

if(defined("FILE_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id553_id'] = new weTagData_selectorAttribute('553', 'id',FILE_TABLE, 'text/webedition', false, ''); }
$GLOBALS['weTagWizard']['attribute']['id383_class'] = new weTagData_textAttribute('383', 'class', false, '');
$GLOBALS['weTagWizard']['attribute']['id384_style'] = new weTagData_textAttribute('384', 'style', false, '');
$GLOBALS['weTagWizard']['attribute']['id628_xml'] = new weTagData_selectAttribute('628', 'xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id810_only'] = new weTagData_selectAttribute('810', 'only', array(new weTagDataOption('href', false, ''), new weTagDataOption('id', false, '')), false, '');