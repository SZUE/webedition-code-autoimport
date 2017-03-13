<?php
/**
 * $Id$
 */
/**
 * This template is shown, when there is no update available. It is possible
 * to start an updaterepeat.
 */
$liveUpdateResponse = [
	'Type' => 'template',
	'Headline' => $GLOBALS['lang']['update']['headline'],
	'Content' => '
<div class="messageDiv">
	' . $GLOBALS['lang']['notification']['updateNotPossibleUntilRelease'] . '
</div>'
];
