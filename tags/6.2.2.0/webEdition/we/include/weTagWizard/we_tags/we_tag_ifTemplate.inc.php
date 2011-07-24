<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectorAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = true;

if(defined("TEMPLATES_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id791_id'] = new weTagData_selectorAttribute('791', 'id',TEMPLATES_TABLE, '', false, ''); }
$GLOBALS['weTagWizard']['attribute']['id792_path'] = new weTagData_textAttribute('792', 'path', false, '');
if(defined("TEMPLATES_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id840_workspaceID'] = new weTagData_selectorAttribute('840', 'workspaceID',TEMPLATES_TABLE, 'folder', false, ''); }
