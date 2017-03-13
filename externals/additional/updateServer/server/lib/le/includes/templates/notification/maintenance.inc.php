<?php
/**
 * $Id$
 */
$liveUpdateResponse = [
	'Type' => 'template',
	'Headline' => $GLOBALS['lang']['notification']['headline'],
	'Content' => '
<div class="messageDiv">
' . $GLOBALS['lang']['notification']['maintenance'] . '
<script>
		alert("' . $GLOBALS['lang']['notification']['maintenance'] . '");
	</script>
</div>
'];
// maintenance
