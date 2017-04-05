<?php
/**
 * $Id: maintenance.inc.php 13564 2017-03-13 17:13:40Z mokraemer $
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
