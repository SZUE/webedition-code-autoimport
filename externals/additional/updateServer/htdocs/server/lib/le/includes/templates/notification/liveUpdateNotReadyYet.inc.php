<?php
/**
 * This template is shown, when there is no update available. It is possible
 * to start an updaterepeat.
 */

$liveUpdateResponse['Type'] = 'template';
$liveUpdateResponse['Headline'] = $GLOBALS['lang']['update']['headline'];
$liveUpdateResponse['Content'] = '
<div class="messageDiv">
	' . $GLOBALS['lang']['notification']['updateNotPossibleUntilRelease'] . '
</div>';
?>