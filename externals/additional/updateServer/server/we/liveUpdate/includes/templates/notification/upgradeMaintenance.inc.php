<?php
/**
 * $Id: upgradeMaintenance.inc.php 13540 2017-03-12 11:48:37Z mokraemer $
 */
$liveUpdateResponse['Type'] = 'template';
$liveUpdateResponse['Headline'] = $GLOBALS['lang']['notification']['headline'];
$liveUpdateResponse['Content'] = '
<div class="messageDiv">
' . $GLOBALS['lang']['notification']['upgradeMaintenance'] . '
</div>
';

