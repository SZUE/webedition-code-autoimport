<?php
/**
 * $Id$
 */
/**
 * This template is shown, when session on server was lost
 * in this template session is gone
 */
$liveUpdateResponse['Type'] = 'template';
$liveUpdateResponse['Headline'] = $GLOBALS['lang']['notification']['headline'];
$liveUpdateResponse['Content'] = $GLOBALS['lang']['notification']['lostSession'] .
	'<script>alert("' . $GLOBALS['lang']['notification']['lostSession'] . '")</script>';

