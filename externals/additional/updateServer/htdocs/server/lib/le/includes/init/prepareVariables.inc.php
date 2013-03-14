<?php

/**
 * Prepare global needed Variables for the LiveUpdate
 */

$updateServerTemplateData = array();

// extract additional variables
if (isset($_REQUEST['reqArray'])) {
	$clientRequestVars = @unserialize(stripslashes(urldecode(base64_decode($_REQUEST['reqArray']))));
	if(!is_array($clientRequestVars)) {
		$clientRequestVars = @unserialize(stripslashes(urldecode($_REQUEST['reqArray'])));
	}
	
} else {
	$clientRequestVars = array();

}
?>