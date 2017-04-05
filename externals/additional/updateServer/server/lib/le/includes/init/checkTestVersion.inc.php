<?php
/**
 * $Id: checkTestVersion.inc.php 13540 2017-03-12 11:48:37Z mokraemer $
 */
/**
 * check if its test from us
 */
if(isset($clientRequestVars['testUpdate']) && $clientRequestVars['testUpdate']){
	// -> ignore if version is published or not
	$_SESSION['testUpdate'] = true;
}

