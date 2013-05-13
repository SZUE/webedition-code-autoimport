<?php

/**
 * check if its test from us
 */

if (isset($clientRequestVars['testUpdate']) && $clientRequestVars['testUpdate']) {
	// -> ignore if version is published or not
	$_SESSION['testUpdate'] = true;

}

?>