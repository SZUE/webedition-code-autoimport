<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_multiSelectorAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_choiceAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/conf/we_conf_language.inc.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = true;
$locales = array();
while ($arr = current($GLOBALS["weFrontendLanguages"])) {
	$locales[] = new weTagDataOption(key($GLOBALS["weFrontendLanguages"]), false, '');
    next($GLOBALS["weFrontendLanguages"]);
}

$GLOBALS['weTagWizard']['attribute']['id116_match'] = new weTagData_choiceAttribute('116', 'match',$locales, false,true, '');
$GLOBALS['weTagWizard']['attribute']['id195_doc'] = new weTagData_selectAttribute('195', 'doc', array(new weTagDataOption('top', false, ''), new weTagDataOption('self', false, '')), false, '');
