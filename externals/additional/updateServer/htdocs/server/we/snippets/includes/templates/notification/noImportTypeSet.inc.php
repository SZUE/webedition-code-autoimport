<?php
/**
 * This template is used, when the registration form of a demo version is
 * requested. This contains mainly a input field for the serial
 */

$liveUpdateResponse['Type'] = 'executeOnline';
$liveUpdateResponse['Code'] = '
<?php

	$Template->addError("' . $GLOBALS['lang']['downloadSnippet']['noImportTypeSet'] . '");
	return LE_WIZARDSTEP_FATAL_ERROR;

?>';

?>