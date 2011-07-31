<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_multiSelectorAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_choiceAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = true;
$locales = array();
foreach($GLOBALS["weFrontendLanguages"] as $lv){
	$locales[] = new weTagDataOption($lv, false, '');
}

$GLOBALS['weTagWizard']['attribute']['id116_match'] = new weTagData_choiceAttribute('116', 'match',$locales, false,true, '');
