
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;
$GLOBALS['weTagWizard']['weTagData']['noDocuLink'] = true;
$GLOBALS['weTagWizard']['weTagData']['DocuLink'] = 'tags.webedition.org/de/<?php print $TOOLNAME;?>/';


$GLOBALS['weTagWizard']['attribute']['id111_name'] = new weTagData_textAttribute('111', 'name', false, '');

