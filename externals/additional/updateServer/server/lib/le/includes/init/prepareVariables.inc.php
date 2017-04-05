<?php
/**
 * $Id: prepareVariables.inc.php 13540 2017-03-12 11:48:37Z mokraemer $
 */
/**
 * Prepare global needed Variables for the LiveUpdate
 */
$updateServerTemplateData = array();

// extract additional variables
if(!empty($_REQUEST['reqArray'])){
	$clientRequestVars = @unserialize(stripslashes(urldecode(base64_decode($_REQUEST['reqArray']))));
	if(!is_array($clientRequestVars)){
		$clientRequestVars = @unserialize(stripslashes(urldecode($_REQUEST['reqArray'])));
	}
} else {
	$clientRequestVars = array();
}
