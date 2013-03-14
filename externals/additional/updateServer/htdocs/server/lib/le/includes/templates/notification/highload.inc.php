<?php

$liveUpdateResponse['Type'] = 'template';
$liveUpdateResponse['Headline'] = $GLOBALS['lang']['notification']['headline'];
$liveUpdateResponse['Content'] = '
<div class="messageDiv">
' . $GLOBALS['lang']['notification']['highload'] . '
<script type="text/JavaScript">
		alert("' . $GLOBALS['lang']['notification']['highload'] . '");
	</script>
</div>
';
// maintenance, maintenance_15
?>