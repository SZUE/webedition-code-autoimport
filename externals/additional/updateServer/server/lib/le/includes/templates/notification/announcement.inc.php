<?php
/**
 * $Id$
 */
$liveUpdateResponse = [
	'Type' => 'template',
	'Headline' => $GLOBALS['lang']['notification']['headline'],
	'Content' => '
<div class="messageDiv">
' . $GLOBALS['lang']['notification']['importantAnnouncement'] . '
<script>
		alert("' . $GLOBALS['lang']['notification']['importantAnnouncement'] . '");
	</script>
</div>
'];
// maintenance