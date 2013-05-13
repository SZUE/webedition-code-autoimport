<?php

$liveUpdateResponse['Type'] = 'template';
$liveUpdateResponse['Headline'] = $GLOBALS['lang']['notification']['headline'];
$liveUpdateResponse['Content'] = '
<div class="messageDiv">
' . $GLOBALS['lang']['notification']['maintenance'] . '
<script type="text/JavaScript">
		alert("' . $GLOBALS['lang']['notification']['maintenance'] . '");
	</script>
</div>
';
// maintenance, maintenance_15
?>