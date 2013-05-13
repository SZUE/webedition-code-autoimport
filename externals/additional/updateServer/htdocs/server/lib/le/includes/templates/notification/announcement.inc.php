<?php

$liveUpdateResponse['Type'] = 'template';
$liveUpdateResponse['Headline'] = $GLOBALS['lang']['notification']['headline'];
$liveUpdateResponse['Content'] = '
<div class="messageDiv">
' . $GLOBALS['lang']['notification']['importantAnnouncement'] . '
<script type="text/JavaScript">
		alert("' . $GLOBALS['lang']['notification']['importantAnnouncement'] . '");
	</script>
</div>
';
// maintenance, maintenance_15
?>