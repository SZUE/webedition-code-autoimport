<?php
/**
 * $Id: lostSession.inc.php 13564 2017-03-13 17:13:40Z mokraemer $
 */
/**
 * This template is shown, when session on server was lost
 * in this template session is gone
 */
$liveUpdateResponse = [
	'Type' => 'template',
	'Headline' => $GLOBALS['lang']['notification']['headline'],
	'Content' => $GLOBALS['lang']['notification']['lostSession'] .
	'<script>alert("' . $GLOBALS['lang']['notification']['lostSession'] . '")</script>',
];

